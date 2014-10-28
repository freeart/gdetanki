<?php

class Feed extends Api
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get($condition)
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
				 ' . $condition . '
				group by p.id
				order by p.pinned desc, p.created desc';

		$sth = $this->db->prepare($sql);

		$sth->execute();

		$data = [];

		$hstoreType = new DB_Type_Pgsql_Hstore(
			new DB_Type_Wrapper_NullToDefault(new DB_Type_String())
		);

		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
			$row["detail"] = $hstoreType->input($row["detail"]);
			$row["comments"] = json_decode($row["comments"]);
			$row["author"] = json_decode($this->redis->get('users:' . $row["authorId"]));
			foreach ($row["comments"] as $key => $value) {
				if ($value->id) {
					$value->detail = $hstoreType->input($value->detail);
					$value->author = json_decode($this->redis->get('users:' . $value->authorId));
				}
			}
			$data[] = $row;
		}

		$sth->closeCursor();

		return $data;
	}
}
