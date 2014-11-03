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
					p.starred,
					p.rating,
					p.created
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
			$row["author"] = json_decode($this->redis->get('users:' . $row["authorId"] . ':info'));
			$data[] = $row;
		}

		$sth->closeCursor();

		return $data;
	}
}
