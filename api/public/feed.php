<?php

class Feed extends Api
{
	public function __construct()
	{
		parent::__construct();
	}

	public function count($condition)
	{
		$sql = 'select count(p.id) as count
				from posts p
				 ' . $condition;

		$sth = $this->db->prepare($sql);

		$sth->execute();

		$row = $sth->fetch(PDO::FETCH_ASSOC);

		$sth->closeCursor();

		if ($row['count'] == 0) {
			return 0;
		} else {
			if ($row['count'] % 10 > 0) {
				return floor($row['count'] / 10) + 1;
			} else {
				return floor($row['count'] / 10);
			}
		}
	}

	public function verify_update($condition)
	{
		$sql = 'select p.id,
					p.detail,
					p."authorId",
					p.pinned,
					p.starred,
					p.rating,
					p.created,
					p.comment_enabled
				from posts p
				left outer join comments c on p.id = c."postId"
				 ' . $condition . '
				group by p.id
				';

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

	public function get($condition, $page)
	{
		$offset = ($page - 1) * 10;

		$sql = 'select p.id,
					p.detail,
					p."authorId",
					p.pinned,
					p.starred,
					p.rating,
					p.created,
					p.comment_enabled
				from posts p
				 ' . $condition . '
				order by p.pinned desc, p.created desc
				limit 10 offset :offset
				';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':offset', $offset, PDO::PARAM_INT);

		$sth->execute();

		$data = [];

		$hstoreType = new DB_Type_Pgsql_Hstore(
			new DB_Type_Wrapper_NullToDefault(new DB_Type_String())
		);

		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
			$row["detail"] = $hstoreType->input($row["detail"]);
			$row["author"] = json_decode($this->redis->get('users:' . $row["authorId"] . ':info'));
			$data[] = $row;
		}

		$sth->closeCursor();

		return $data;
	}

	public function getPagination($page = 1, $pages = 1)
	{

		$start_range = $page - floor(7 / 2);
		$end_range = $page + floor(7 / 2);
		if ($start_range <= 0) {
			$end_range += abs($start_range) + 1;
			$start_range = 1;
		}
		if ($end_range > $pages) {
			$start_range -= $end_range - $pages;
			$end_range = $pages;
		}
		$range = range($start_range, $end_range);

		return $range;
	}
}
