<?php

class Users extends Api
{
	private $current = array();

	public function __construct($id)
	{
		parent::__construct();

		if (!empty($id)) {
			$sql = 'select * from users where id = :user_id';

			$sth = $this->db->prepare($sql);

			$sth->bindParam(':user_id', $id, PDO::PARAM_INT);

			$sth->execute();

			$hstoreType = new DB_Type_Pgsql_Hstore(
				new DB_Type_Wrapper_NullToDefault(new DB_Type_String())
			);

			$data = $sth->fetch(PDO::FETCH_ASSOC);
			$data["detail"] = $hstoreType->input($data["detail"]);

			$sth->closeCursor();

			$this->current = $data;
		}
	}

	public function profile()
	{
		return $this->current;
	}

	public function read()
	{
		$postId = $this->request->get('id', 'integer');

		return $this->post->get($postId);
	}

	public function feed()
	{
		$filter = $this->request->get('filter', 'string');

		return $this->feed->get();
	}
}
