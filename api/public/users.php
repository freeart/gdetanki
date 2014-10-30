<?php

class Users extends Api
{
	private $current;

	public function __construct($id)
	{
		parent::__construct();

		$id = $this->session->get('user_id');

		if ($id > 0) {
			$data = json_decode($this->redis->get('users:' . $id . ':info'));
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

		$map = array(
			'top' => 'where p.starred is true',
			'new' => 'where p.created::date = CURRENT_DATE',
			'hot' => 'where p.rating > 9'
		);

		return $this->feed->get(array_key_exists($filter, $map) ? $map[$filter] : '');
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

		return array(
			"body" => array("mode" => "refresh")
		);
	}

	public function signin()
	{
		$login = strtolower($this->request->get('login', 'enject'));
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

		if ($id > 0) {
			return array(
				"body" => array("mode" => "redirect", "url" => "/"),
				"data" => array("id" => $id)
			);
		} else {
			return array(
				"error" => 1,
				"message" => "Ошибка логина или пароля"
			);
		}
	}

	public function rating()
	{
		$id = $this->request->get('id', 'integer');
		$value = $this->request->get('value', 'integer');
		$value = $value > 0 ? 1 : -1;

		$bulk = $this->redis->hmget('users:' . $this->current->id . ':rating:' . $id, 'v', 'r');
		$bulk[1] = $bulk[1] === null ? 2 : $bulk[1];

		if ($bulk[0] != $value && $bulk[1] > 0) {
			$value = $bulk[1] == 1 ? $value * 2 : $value;

			$this->redis->hmset('users:' . $this->current->id . ':rating:' . $id, 'v', $value, 'r', $bulk[1] - 1);
			$rating = $this->post->rating($id, $value);
			return array(
				'[data-id=' . $id . '] .social-bar span' => array('mode' => 'replace', 'html' => $rating)
			);
		} else {
			return array(
				"error" => 1,
				"message" => $bulk[1] == 1 ? "Вы можете только изменить голос (1 раз)" : "Вы уже голосовали"
			);
		}
	}

	public function pin()
	{
		$id = $this->request->get('id', 'integer');
		$value = $this->request->get('value', 'boolean');

		$result = $this->post->pin($id, $value);

		return array(
			"error" => $value == $result ? 0 : 1,
			"result" => $result
		);
	}

	public function star()
	{
		$id = $this->request->get('id', 'integer');
		$value = $this->request->get('value', 'boolean');

		$result = $this->post->star($id, $value);

		return array(
			"error" => $value == $result ? 0 : 1,
			"result" => $result
		);
	}

	public function editpost()
	{
		$id = $this->request->get('id', 'integer');

		$this->template->assign('this', $this);
		$this->template->assign('post', $this->post->get($id));

		$this->template->fetch('functions.tpl');

		return array(
			".feed-wrap[data-id=$id]" => array(
				"mode" => "replaceWith",
				"html" => $this->template->fetch("block/post/edit.tpl")
			)
		);
	}

	public function removepost()
	{
		$id = $this->request->get('id', 'integer');

		$result = $this->post->remove($id);

		return array(
			"error" => $result === true ? 0 : 1,
			"result" => $result
		);
	}
}
