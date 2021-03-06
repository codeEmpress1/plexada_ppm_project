<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Milestones Model
 *
 * @property \App\Model\Table\ProjectDetailsTable&\Cake\ORM\Association\BelongsTo $ProjectDetails
 * @property \App\Model\Table\LovTable&\Cake\ORM\Association\BelongsTo $Lov
 * @property \App\Model\Table\LovTable&\Cake\ORM\Association\BelongsTo $Lov
 *
 * @method \App\Model\Entity\Milestone get($primaryKey, $options = [])
 * @method \App\Model\Entity\Milestone newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Milestone[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Milestone|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Milestone saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Milestone patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Milestone[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Milestone findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MilestonesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('milestones');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ProjectDetails', [
            'foreignKey' => 'project_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Lov', [
            'foreignKey' => 'status_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Lov', [
            'foreignKey' => 'trigger_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('record_number')
            ->maxLength('record_number', 100)
            ->allowEmptyString('record_number');

        $validator
            ->integer('amount')
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

        $validator
            ->scalar('payment')
            ->maxLength('payment', 100)
            ->allowEmptyString('payment');

        $validator
            ->scalar('description')
            ->maxLength('description', 100)
            ->allowEmptyString('description');

        $validator
            ->scalar('achievement')
            ->maxLength('achievement', 100)
            ->allowEmptyString('achievement');

        $validator
            ->date('completed_date')
            ->allowEmptyDate('completed_date');

        $validator
            ->date('expected_completion_date')
            ->allowEmptyDate('expected_completion_date');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['project_id'], 'ProjectDetails'));
        $rules->add($rules->existsIn(['status_id'], 'Lov'));
        $rules->add($rules->existsIn(['trigger_id'], 'Lov'));

        return $rules;
    }
}
