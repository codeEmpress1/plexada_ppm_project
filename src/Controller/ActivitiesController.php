<?php

namespace App\Controller;

use App\Controller\AppController;

/**
 * Activities Controller
 *
 * @property \App\Model\Table\ActivitiesTable $Activities
 *
 * @method \App\Model\Entity\Activity[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ActivitiesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['ProjectDetails', 'Staff', 'Lov', 'Users'],
        ];
        $activities = $this->paginate($this->Activities);

        $this->set(compact('activities'));
    }

    /**
     * View method
     *
     * @param string|null $id Activity id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $activity = $this->Activities->get($id, [
            'contain' => ['ProjectDetails', 'Staff', 'Lov', 'Users'],
        ]);

        $this->set('activity', $activity);
    }

    /**
     * Add method
     *
     * @param null $project_id
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($project_id = null)
    {
        $activity = $this->Activities->newEntity();
        if ($this->request->is('post')) {
            $activity = $this->Activities->patchEntity($activity, $this->request->getData());
            if ($this->Activities->save($activity)) {
                $this->Flash->success(__('The activity has been saved.'));

                //                return $this->redirect(['controller' => 'ProjectDetails', 'action' => 'view', $project_id]);
                return $this->redirect($this->referer());
            }
            $this->Flash->error(__('The activity could not be saved. Please, try again.'));
            //            return $this->redirect(['controller' => 'ProjectDetails', 'action' => 'view', $project_id]);
            return $this->redirect($this->referer());
        }
        $projectDetails = $this->Activities->ProjectDetails->find('list', ['limit' => 200]);
        $staff = $this->Activities->Staff->find('list', ['limit' => 200]);
        $priority = $this->Activities->Priorities->find('list', ['limit' => 200]);
        $status = $this->Activities->Statuses->find('list', ['limit' => 200]);
        $users = $this->Activities->Users->find('list', ['limit' => 200]);
        $this->set(compact('activity', 'projectDetails', 'staff', 'priority', 'status', 'users', 'project_id'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Activity id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $activity = $this->Activities->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $activity = $this->Activities->patchEntity($activity, $this->Activities->identify($this->request->getData()));
            if ($this->Activities->save($activity)) {
                $this->Flash->success(__('The activity has been saved.'));

                //                return $this->redirect(['action' => 'index']);
                return $this->redirect($this->referer());
            }
            $this->Flash->error(__('The activity could not be saved. Please, try again.'));
            return $this->redirect($this->referer());
        }
        $projectDetails = $this->Activities->ProjectDetails->find('list', ['limit' => 200]);
        $staff = $this->Activities->Staff->find('list', ['limit' => 200]);
        //        $lov = $this->Activities->Lov->find('list', ['limit' => 200]);
        $priority = $this->Activities->Priorities->find('list', [
            'conditions' => ['Priorities.lov_type' => 'priority'],
            'limit' => 200
        ]);
        $status = $this->Activities->Statuses->find(
            'list',
            [
                'conditions' => ['Statuses.lov_type' => 'project_status'],
                'limit' => 200
            ]
        );
        $users = $this->Activities->Users->find('list', ['limit' => 200]);
        $this->set(compact('activity', 'projectDetails', 'staff', 'users', 'priority', 'status'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Activity id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $activity = $this->Activities->get($id);
        if ($this->Activities->delete($activity)) {
            $this->Flash->success(__('The activity has been deleted.'));
        } else {
            $this->Flash->error(__('The activity could not be deleted. Please, try again.'));
        }

        //        return $this->redirect(['action' => 'index']);
        return $this->redirect($this->referer());
    }
}