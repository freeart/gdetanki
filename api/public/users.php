<?php

class Users extends Api
{
	private $current;

	public function __construct($id)
	{
		parent::__construct();

		$id = $this->session->get('user_id');
		if ($id > 0) {
			$data = json_decode($this->redis->get('users:' . $id));
			$data->id = $id;

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

	public function logged($str = null)
	{
		if ($str) {
			return !empty($this->current) ? $str : '/registration';
		} else {
			return !empty($this->current) && $this->current->id > 0;
		}
	}

	public function signout()
	{
		$this->session->remove('user_id');

		header('Location: ' . $_SERVER['REQUEST_URI']);
	}

	public function signin()
	{
		$login = $this->request->get('login', 'enject');
		$password = $this->request->get('password', 'enject');
		$checksum = md5($login . $password . 'E4fgg656@#%uyghfddhghcv');

		$sql = 'select id
				from users
				where login = :login and password = :checksum';

		$sth = $this->db->prepare($sql);

		$sth->bindParam(':login', $login, PDO::PARAM_STR);
		$sth->bindParam(':checksum', $checksum, PDO::PARAM_STR);

		$sth->execute();

		$data = $sth->fetch(PDO::FETCH_ASSOC);
		$id = $data["id"];
		if ($id > 0) {
			$this->session->set('user_id', $id);
			//$data = json_decode($this->redis->get('users:' . $id));
			//$data->id = $id;
		}

		$sth->closeCursor();

		header('Location: ' . $_SERVER['REQUEST_URI']);
	}
}
