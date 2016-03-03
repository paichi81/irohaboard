<?php
/**
 * iroha Board Project
 *
 * @author        Kotaro Miura
 * @copyright     2015-2016 iroha Soft, Inc. (http://irohasoft.jp)
 * @link          http://irohasoft.jp/irohaboard
 * @license       http://www.gnu.org/licenses/gpl-3.0.en.html GPL License
 */

App::uses('AppController', 'Controller');

/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController
{

	public $components = array(
			'Session',
			'Paginator',
			'Auth' => array(
					'allowedActions' => array(
							'index',
							'login',
							'add'
					)
			)
	);

	public function beforeFilter()
	{
		parent::beforeFilter();
		// ユーザー自身による登録とログアウトを許可する
		$this->Auth->allow('add', 'logout');
	}

	public function index()
	{
		$this->redirect("/users_courses");
	}

	public function view($id = null)
	{
		if (! $this->User->exists($id))
		{
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array(
				'conditions' => array(
						'User.' . $this->User->primaryKey => $id
				)
		);
		$this->set('user', $this->User->find('first', $options));
	}

	public function setting()
	{
		$this->admin_setting();
	}

	public function admin_delete($id = null)
	{
		$this->User->id = $id;
		if (! $this->User->exists())
		{
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->User->delete())
		{
			$this->Flash->success(__('The user has been deleted.'));
		}
		else
		{
			$this->Flash->error(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array(
				'action' => 'index'
		));
	}

	public function logout()
	{
		$this->redirect($this->Auth->logout());
	}

	public function login()
	{
		$options = array(
			'conditions' => array(
					'User.username' => 'root'
			)
		);

		$data = $this->User->find('first', $options);

		if(!$data)
		{
			// 管理者アカウントが存在しない場合、管理者アカウントを作成
			$data = array(
				'course_id' => $this->Session->read('Iroha.course_id'),
				'username' => 'root',
				'password' => 'irohaboard',
				'name' => 'root',
				'role' => 'admin',
				'email' => 'info@example.com'
			);

			$this->User->save($data);
		}

		if ($this->request->is('post'))
		{
			debug($this->request->data);
			debug($this->Auth->login());
			// debug($this->Auth);
			if ($this->Auth->login())
			{
				$this->Session->delete('Auth.redirect');
				$this->redirect($this->Auth->redirect());
			}
			else
			{
				$this->Flash->error(__('Invalid username or password, try again'));
			}
		}
	}

	public function admin_add()
	{
		$this->admin_edit();
		$this->render('admin_edit');
	}

	public function admin_index()
	{
		$this->User->recursive = 0;

		/*
		$conditions = array(
				'User.group_id' => $this->Session->read("Iroha.group_id")
		);

		$this->set('users', $this->Paginator->paginate($conditions));
		*/

		$this->paginate = array(
			'User' => array(
				'fields' => array('*', 'UserGroup.group_count', 'UserCourse.course_count'),
				'conditions' => array(),
				'limit' => 10,
				'order' => 'group_count',
				'joins' => array(
					array('type' => 'LEFT OUTER', 'alias' => 'UserGroup',
							'table' => '(SELECT user_id, COUNT(*) as group_count FROM ib_users_groups GROUP BY user_id)',
							'conditions' => 'User.id = UserGroup.user_id'),
					array('type' => 'LEFT OUTER', 'alias' => 'UserCourse',
							'table' => '(SELECT user_id, COUNT(*) as course_count FROM ib_users_courses GROUP BY user_id)',
							'conditions' => 'User.id = UserCourse.user_id')
				))
		);



// 		if (isset($this->request->named['sort']) && $this->request->named['sort'] == 'UserGroup.group_count')
// 		{
// 			debug(array('UserGroup.group_count' => $this->request->named['dir']));

// 			$this->paginate['order'] = 'UserGroup.group_count';
// 		}

		$result = $this->paginate();

		// 独自カラムの場合、自動でソートされないため、個別の実装が必要
		if (isset($this->request->named['sort']) && $this->request->named['sort'] == 'UserGroup.group_count')
		{
			$result = Set::sort($result, '/UserGroup/group_count', $this->request->named['direction']);
		}

		if (isset($this->request->named['sort']) && $this->request->named['sort'] == 'UserCourse.course_count')
		{
			$result = Set::sort($result, '/UserCourse/course_count', $this->request->named['direction']);
		}

		//debug($result);

		$this->set('users', $result);

		//debug($this->Paginator->paginate());
	}

	public function admin_welcome()
	{}

	public function admin_edit($id = null)
	{
		if ($this->action == 'admin_edit' && ! $this->User->exists($id))
		{
			throw new NotFoundException(__('Invalid user'));
		}

		if ($this->request->is(array(
				'post',
				'put'
		)))
		{
			if($this->action == 'admin_add')
			{
				//$this->request->data['User']['group_id'] = $this->Session->read('Iroha.group_id');
			}

			if ($this->request->data['User']['new_password'] !== '')
				$this->request->data['User']['password'] = $this->request->data['User']['new_password'];

			if ($this->User->save($this->request->data))
			{
				$this->Flash->success(__('ユーザ情報が保存されました'));

				unset($this->request->data['User']['new_password']);

				return $this->redirect(array(
						'action' => 'index'
				));
			}
			else
			{
				$this->Flash->error(__('The user could not be saved. Please, try again.'));
			}
		}
		else
		{
			$options = array(
					'conditions' => array(
							'User.' . $this->User->primaryKey => $id
					)
			);
			$this->request->data = $this->User->find('first', $options);
		}

		$courses = $this->User->Course->find('list');
		$groups = $this->User->Group->find('list');
		$this->set(compact('courses', 'groups'));
	}

	public function admin_setting()
	{
		if ($this->request->is(array(
				'post',
				'put'
		)))
		{
			//debug($this->request->data);
			$this->request->data['User']['id'] = $this->Session->read('Auth.User.id');

			if ($this->request->data['User']['new_password'] !== '')
			{
				$this->request->data['User']['password'] = $this->request->data['User']['new_password'];
				if ($this->User->save($this->request->data))
				{
					$this->Flash->success(__('パスワードが保存されました'));
				}
				else
				{
					$this->Flash->error(__('The user could not be saved. Please, try again.'));
				}
			}
			else
			{
				$this->Flash->error(__('パスワードを入力して下さい'));
			}
		}
		else
		{
			$options = array(
				'conditions' => array(
						'User.' . $this->User->primaryKey => $this->Session->read('Auth.User.id')
				)
			);
			$this->request->data = $this->User->find('first', $options);
		}
	}

	public function admin_login()
	{
		$this->login();
	}

	public function admin_logout()
	{
		$this->logout();
	}
}