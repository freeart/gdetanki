<?php

class Post extends Api
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get($id)
	{
		$sql = 'select p.id,
					p.detail,
					p."authorId",
					p.pinned,
					p.rating,
					p.created,
					json_agg((select x from (select c.id, c.detail, c."authorId", c.created) x)) "comments"
				from posts p
				left outer join comments c on p.id = c."postId"
				where p.id = :post_id
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
		$data["author"] = json_decode($this->redis->get('users:' . $data["authorId"]));
		foreach ($data["comments"] as $key => $value) {
			if ($value->id) {
				$value->detail = $hstoreType->input($value->detail);
				$value->author = json_decode($this->redis->get('users:' . $value->authorId));
			}
		}

		$sth->closeCursor();

		return $data;
	}
}
