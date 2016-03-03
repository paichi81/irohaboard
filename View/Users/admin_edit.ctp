<?php echo $this->element('admin_menu');?>
<?php echo $this->Html->css( 'select2.min.css');?>
<?php echo $this->Html->script( 'select2.min.js');?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
	$(function (e) {
		$('#GroupGroup').select2();
		$('#CourseCourse').select2();
		// パスワードの自動復元を防止
		setTimeout('$("#UserNewPassword").val("");',100);
	});
<?php $this->Html->scriptEnd(); ?>
<div class="users form">
<?php echo $this->Html->link(__('<< 戻る'), array('action' => 'index'))?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<?php echo ($this->action == 'admin_edit') ? __('編集') :  __('新規グループ'); ?>
		</div>
		<div class="panel-body">
			<?php echo $this->Form->create('User', Configure::read('form_defaults')); ?>
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('username',				array('label' => 'ログインID'));
				echo $this->Form->input('User.new_password',	array('label' => 'パスワード', 'type' => 'password', 'autocomplete' => 'off'));
				echo $this->Form->input('name',					array('label' => '氏名'));
				echo $this->Form->input('role',	array(
					'type' => 'radio',
					'before' => '<label class="col col-md-3 control-label">公開設定</label>',
					'separator'=>"　", 
					'disabled'=>false, 
					'legend' => false,
					'class' => false,
					'options' => Configure::read('user_role')
					)
				);

				
				echo $this->Form->input('email',				array('label' => 'メールアドレス'));
				echo $this->Form->input('comment',				array('label' => '備考'));
				echo $this->Form->input('Group',				array('label' => '所属グループ',	'size' => 20));
				echo $this->Form->input('Course',				array('label' => '受講コース', 'size' => 20));
			?>
			<div class="form-group">
				<div class="col col-md-9 col-md-offset-3">
					<?php echo $this->Form->submit('保存', Configure::read('form_submit_defaults')); ?>
				</div>
			</div>
		</div>
	</div>
</div>