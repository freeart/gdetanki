<?php

class Post extends Api
{
	public function __construct()
	{
		parent::__construct();
	}

	public function comment($post_id, $author_id, $text)
	{
		$sql = 'insert into comments ("postId", "authorId", text) values(:post_id, :author_id, :text) returning id, "authorId", text';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':post_id', $post_id, PDO::PARAM_INT);
		$sth->bindParam(':author_id', $author_id, PDO::PARAM_INT);
		$sth->bindParam(':text', $text, PDO::PARAM_STR);

		$sth->execute();

		$data = $sth->fetch(PDO::FETCH_ASSOC);

		$sth->closeCursor();

		$comment = new stdClass();
		if ($data["id"]) {
			$comment->id = $data["id"];
			$comment->author = json_decode($this->redis->get('users:' . $data["authorId"] . ':info'));
			$comment->text = $data["text"];
		}

		return $comment;
	}

	public function rating($userId, $id, $value)
	{
		if (empty($userId)) return null;

		$sql = 'update posts set rating = rating + (:value) where id = :post_id returning rating';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':post_id', $id, PDO::PARAM_INT);
		$sth->bindParam(':value', $value, PDO::PARAM_INT);

		$sth->execute();

		$data = $sth->fetch(PDO::FETCH_ASSOC);

		$sth->closeCursor();

		return $data['rating'];
	}

	public function getComment($comment_id)
	{
		$sql = 'select c.id,
					c.text,
					c."authorId",
					c."postId"
				from comments c
				where c.id = :comment_id';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);

		$sth->execute();

		$data = $sth->fetch(PDO::FETCH_ASSOC);

		$sth->closeCursor();

		$comment = new stdClass();
		if ($data["id"]) {
			$comment->id = $data["id"];
			$comment->postId = $data["postId"];
			$comment->author = json_decode($this->redis->get('users:' . $data["authorId"] . ':info'));
			$comment->text = $data["text"];
		}

		return $comment;
	}

	public function get($id)
	{
		$sql = 'select p.id,
					p.detail,
					p."authorId",
					p.pinned,
					p.starred,
					p.rating,
					p.created,
					p.comment_enabled,
					json_agg((select x from (select c.id, c.text, c."authorId", c.created) x)) "comments"
				from posts p
				left outer join comments c on p.id = c."postId"
				where p.id = :post_id
				and p.deleted = false
				group by p.id
				order by p.created desc';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':post_id', $id, PDO::PARAM_INT);

		$sth->execute();

		$hstoreType = new DB_Type_Pgsql_Hstore(
			new DB_Type_Wrapper_NullToDefault(new DB_Type_String())
		);

		$data = $sth->fetch(PDO::FETCH_ASSOC);
		$data["detail"] = $hstoreType->input($data["detail"]);
		$data["comments"] = json_decode($data["comments"]);
		$data["author"] = json_decode($this->redis->get('users:' . $data["authorId"] . ':info'));
		foreach ($data["comments"] as $key => $value) {
			if ($value->id) {
				$value->author = json_decode($this->redis->get('users:' . $value->authorId . ':info'));
			}
		}

		$sth->closeCursor();

		return $data;
	}

	public function pin($userId, $id, $value)
	{
		if (empty($userId)) return null;

		$sql = 'update posts set pinned = :value where id = :post_id returning pinned';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':post_id', $id, PDO::PARAM_INT);
		$sth->bindParam(':value', $value, PDO::PARAM_BOOL);

		$sth->execute();

		$data = $sth->fetch(PDO::FETCH_ASSOC);

		$sth->closeCursor();

		return $data["pinned"] == 'true' ? true : false;
	}

	public function star($userId, $id, $value)
	{
		if (empty($userId)) return null;

		$sql = 'update posts set starred = :value where id = :post_id returning starred';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':post_id', $id, PDO::PARAM_INT);
		$sth->bindParam(':value', $value, PDO::PARAM_BOOL);

		$sth->execute();

		$data = $sth->fetch(PDO::FETCH_ASSOC);

		$sth->closeCursor();

		return $data["starred"] == 'true' ? true : false;
	}

	public function add($userId, $detail)
	{
		if (empty($userId)) return null;

		$this->db;

		$hstoreType = new DB_Type_Pgsql_Hstore(new DB_Type_String());
		$hstore_detail = $hstoreType->output($detail);

		$sql = 'insert into posts (detail, "authorId") values(\'' . $hstore_detail . '\'::hstore, :author_id) returning id';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':author_id', $userId, PDO::PARAM_INT);

		$sth->execute();

		$data = $sth->fetch(PDO::FETCH_ASSOC);

		$sth->closeCursor();

		return $data["id"];
	}

	public function edit($userId, $id, $detail)
	{
		if (empty($userId)) return null;

		$this->db;

		$hstoreType = new DB_Type_Pgsql_Hstore(new DB_Type_String());
		$hstore_detail = $hstoreType->output($detail);

		$sql = 'update posts set detail = \'' . $hstore_detail . '\'::hstore where id = :post_id returning id';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':post_id', $id, PDO::PARAM_INT);

		$sth->execute();

		$data = $sth->fetch(PDO::FETCH_ASSOC);

		$sth->closeCursor();

		return $data["id"];
	}

	public function remove($userId, $id)
	{
		if (empty($userId)) return null;

		$sql = 'update posts set deleted = true where id = :post_id returning deleted';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':post_id', $id, PDO::PARAM_INT);

		$sth->execute();

		$data = $sth->fetch(PDO::FETCH_ASSOC);

		$sth->closeCursor();

		return $data["deleted"] == 'true' ? true : false;
	}

	public function comment_enabled($userId, $id, $value)
	{
		if (empty($userId)) return null;

		$sql = 'update posts set comment_enabled = :value where id = :post_id returning comment_enabled';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':post_id', $id, PDO::PARAM_INT);
		$sth->bindParam(':value', $value, PDO::PARAM_BOOL);

		$sth->execute();

		$data = $sth->fetch(PDO::FETCH_ASSOC);

		$sth->closeCursor();

		return $data["comment_enabled"] == 'true' ? true : false;
	}
}
