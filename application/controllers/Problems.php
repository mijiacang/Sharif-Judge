<?php
/**
 * Sharif Judge online judge
 * @file Problems.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Problems extends CI_Controller
{

	private $username;
	private $assignment;
	private $user_level;


	// ------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		$this->load->driver('session');
		if ( ! $this->session->userdata('logged_in')) // if not logged in
			redirect('login');

		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);
	}


	// ------------------------------------------------------------------------


	/**
	 * Displays detail description of given problem
	 *
	 * @param int $assignment_id
	 * @param int $problem_id
	 */
	public function index($assignment_id = NULL, $problem_id = 1)
	{

		// If no assignment is given, use selected assignment
		if ($assignment_id === NULL)
			$assignment_id = $this->assignment['id'];

		$data = array(
			'username' => $this->username,
			'user_level' => $this->user_level,
			'all_assignments' => $this->assignment_model->all_assignments(),
			'all_problems' => $this->assignment_model->all_problems($assignment_id),
			'title' => 'Problem '.$problem_id,
			'assignment' => $this->assignment,
			'description_assignment' => $this->assignment_model->assignment_info($assignment_id),
			'style' => 'main.css',
		);

		if ( ! is_numeric($problem_id) || $problem_id < 1 || $problem_id > $data['description_assignment']['problems'])
			show_404();

		$data['problem'] = array(
			'id' => $problem_id,
			'description' => '<p>Description not found</p>'
		);

		$path = rtrim($this->settings_model->get_setting('assignments_root'),'/')."/assignment_{$assignment_id}/p{$problem_id}/desc.html";
		if (file_exists($path))
			$data['problem']['description'] = file_get_contents($path);

		$this->load->view('templates/header', $data);
		$this->load->view('pages/problems', $data);
		$this->load->view('templates/footer');
	}


	// ------------------------------------------------------------------------


	/**
	 * Edit problem description as html
	 *
	 * @param int $assignment_id
	 * @param int $problem_id
	 */
	public function edit_html($assignment_id = NULL, $problem_id = 1)
	{
		if ($this->user_level <= 1)
			show_404();

		if ($assignment_id === NULL)
			$assignment_id = $this->assignment['id'];

		$data = array(
			'username' => $this->username,
			'user_level' => $this->user_level,
			'all_assignments' => $this->assignment_model->all_assignments(),
			'title' => 'Edit Problem Description (HTML)',
			'assignment' => $this->assignment,
			'description_assignment' => $this->assignment_model->assignment_info($assignment_id),
			'style' => 'main.css',
		);

		if ( ! is_numeric($problem_id) || $problem_id < 1 || $problem_id > $data['description_assignment']['problems'])
			show_404();

		$this->form_validation->set_rules('text', 'text' ,'xss_clean');
		if ($this->form_validation->run())
		{
			$this->assignment_model->save_problem_description($assignment_id, $problem_id, $this->input->post('text'), 'html');
			redirect('problems/'.$assignment_id.'/'.$problem_id);
		}

		$data['problem'] = array(
			'id' => $problem_id,
			'description' => ''
		);

		$path = rtrim($this->settings_model->get_setting('assignments_root'),'/')."/assignment_{$assignment_id}/p{$problem_id}/desc.html";
		if (file_exists($path))
			$data['problem']['description'] = file_get_contents($path);


		$this->load->view('templates/header', $data);
		$this->load->view('pages/admin/edit_problem_html', $data);
		$this->load->view('templates/footer');

	}


	// ------------------------------------------------------------------------


	/**
	 * Edit problem description as markdown
	 *
	 * @param int $assignment_id
	 * @param int $problem_id
	 */
	public function edit_md($assignment_id = NULL, $problem_id = 1)
	{
		if ($this->user_level <= 1)
			show_404();

		if ($assignment_id === NULL)
			$assignment_id = $this->assignment['id'];

		$data = array(
			'username' => $this->username,
			'user_level' => $this->user_level,
			'all_assignments' => $this->assignment_model->all_assignments(),
			'title' => 'Edit Problem Description (Markdown)',
			'assignment' => $this->assignment,
			'description_assignment' => $this->assignment_model->assignment_info($assignment_id),
			'style' => 'main.css',
		);

		if ( ! is_numeric($problem_id) || $problem_id < 1 || $problem_id > $data['description_assignment']['problems'])
			show_404();

		$this->form_validation->set_rules('text', 'text' ,'xss_clean');
		if ($this->form_validation->run())
		{
			$this->assignment_model->save_problem_description($assignment_id, $problem_id, $this->input->post('text'), 'markdown');
			redirect('problems/'.$assignment_id.'/'.$problem_id);
		}

		$data['problem'] = array(
			'id' => $problem_id,
			'description' => ''
		);

		$path = rtrim($this->settings_model->get_setting('assignments_root'),'/')."/assignment_{$assignment_id}/p{$problem_id}/desc.md";
		if (file_exists($path))
			$data['problem']['description'] = file_get_contents($path);

		$this->load->view('templates/header', $data);
		$this->load->view('pages/admin/edit_problem_md', $data);
		$this->load->view('templates/footer');

	}

}