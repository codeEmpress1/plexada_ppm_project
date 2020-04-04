<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Vendor[]|\Cake\Collection\CollectionInterface $vendors
 */

$this->start('sidebar');
echo $this->element('sidebar/default');
$this->end();
$this->start('navbar');
echo $this->element('navbar/default');
$this->end();
?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?= __('Donors') ?>
                <div class="btn-group" role="group" aria-label="Basic example">
                    <?= $this->Html->link(__('<i class="fa fa-plus fa-lg"></i>'), ['action' => 'add'], ['class' => 'btn btn-light overlay', 'title' => 'Add', 'escape' => false]) ?>

                </div></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table cellpadding="0" cellspacing="0" class="table table-bordered dataTable" role="grid" aria-describedby="dataTable_info">

                    <thead>
                    <tr>

                        <th scope="col"><?= __('Company Name') ?></th>
                        <th scope="col"><?= __('Address') ?></th>
                        <th scope="col"><?= __('State') ?></th>
                        <th scope="col"><?= __('Country') ?></th>
                        <th scope="col"><?= __('Phone Number') ?></th>
                        <th scope="col"><?= __('Action') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($vendors as $vendor): ?>
                        <tr>
                            <td><?= h($vendor->company_name) ?></td>
                            <td><?= h($vendor->address) ?></td>
                            <td><?= h($vendor->state) ?></td>
                            <td><?= h($vendor->country) ?></td>
                            <td><?= h($vendor->phone_no) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('<i class="fa fa-pencil fa-lg"></i>'), ['action' => 'edit', $vendor->id], ['class' => 'btn btn-outline-warning btn-sm overlay', 'title' => 'Edit', 'escape' => false]) ?>

                                <?= $this->Form->postLink(__("<i class='fa fa-trash-o fa-lg'></i>"), ['action' => 'delete', $vendor->id], ['confirm' => __('Are you sure you want to delete # {0}?', $vendor->id), 'escape' => false, 'class' => 'btn btn-outline-danger btn-sm']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- overlayed element -->
<div id="dialogModal">
    <!-- the external content is loaded inside this tag -->
    <div id="contentWrap">
        <?= $this->Modal->create(['id' => 'MyModal4', 'size' => 'modal-lg']) ?>
        <?= $this->Modal->body()// No header ?>
        <?= $this->Modal->footer()// Footer with close button (default) ?>
        <?= $this->Modal->end() ?>
    </div>
</div>
<script>
    $(".overlay").click(function(event){
        event.preventDefault();
        //load content from href of link
        $('#contentWrap .modal-body').load($(this).attr("href"), function(){
            $('.projectDetails .large-9, .projectDetails .medium-8, .projectDetails .columns, .projectDetails .content').removeClass()
            $('#MyModal4').modal('show')
        });
    });
    $(document).ready(function() {
        $('.dataTable').DataTable();
    } );
</script>
