<?php echo $this->element('admin_menu');?>
<?php $this->start('css-embedded'); ?>
<style type='text/css'>
	#RecordFromDateYear,
	#RecordToDateYear
	{
		width		: 100px;
	}
	
	#RecordFromDateMonth,
	#RecordToDateMonth,
	#RecordFromDateDay,
	#RecordToDateDay
	{
		width		: 80px;
	}
	
	#RecordCourseId
	{
		max-width	: 200px;
	}
	
	#RecordGroupId
	{
		max-width	: 150px;
	}
	
	#RecordUserId
	{
		max-width	: 120px;
	}
	
	input[type='text'], textarea,
	.form-control, 
	label
	{
		font-size	: 12px;
		font-weight	: normal;
		height		: 30px;
		padding		: 4px;
	}
	
	.ib-search-buttons
	{
		float		: right;
	}
	
	.ib-search-buttons .btn
	{
		margin-right: 10px;
	}
	
	table tr td
	{
		padding		: 5px;
	}
</style>
<?php $this->end(); ?>
<?php $this->start('script-embedded'); ?>
<script>
	function openRecord(course_id, user_id)
	{
		window.open(
			'<?php echo Router::url(array('controller' => 'contents', 'action' => 'record')) ?>/'+course_id+'/'+user_id,
			'irohaboard_record',
			'width=1000, height=700, menubar=no, toolbar=no, scrollbars=yes'
		);
	}
	
	function downloadCSV()
	{
		var url = '<?php echo Router::url(array('action' => 'csv')) ?>/' + $('#MembersEventEventId').val() + '/' + $('#MembersEventStatus').val() + '/' + $('#MembersEventUsername').val();
		$("#RecordCmd").val("csv");
		$("#RecordIndexForm").submit();
		$("#RecordCmd").val("");
	}
</script>
<?php $this->end(); ?>
<div class="records index">
	<div class="ib-page-title"><?php echo __('学習履歴一覧'); ?></div>
	<div class="ib-horizontal">
		<?php
			echo $this->Form->create('Record', array('action' => 'index'));
			echo '<div class="ib-search-buttons">';
			echo $this->Form->submit(__('検索'),	array('class' => 'btn btn-info', 'div' => false));
			echo $this->Form->hidden('cmd');
			echo '<button type="button" class="btn btn-default" onclick="downloadCSV()">'.__('CSV出力').'</button>';
			echo '</div>';
			echo $this->Form->input('course_id',	array('label' => 'コース :', 'options'=>$courses, 'selected'=>$course_id, 'empty' => '全て', 'required'=>false, 'class'=>'form-control'));
			echo $this->Form->input('group_id',		array('label' => 'グループ :', 'options'=>$groups, 'selected'=>$group_id, 'empty' => '全て', 'required'=>false, 'class'=>'form-control'));
			echo $this->Form->input('user_id',		array('label' => 'ユーザ :', 'options'=>$users, 'selected'=>$user_id, 'empty' => '全て', 'required'=>false, 'class'=>'form-control'));
			echo $this->Form->input('contenttitle',	array('label' => 'コンテンツ名 :', 'value'=>$contenttitle, 'class'=>'form-control'));
			echo '<div class="ib-search-date-container">';
			echo $this->Form->input('from_date', array(
				'type' => 'date',
				'dateFormat' => 'YMD',
				'monthNames' => false,
				'timeFormat' => '24',
				'minYear' => date('Y') - 5,
				'maxYear' => date('Y'),
				'separator' => ' / ',
				'label'=> '対象日時 : ',
				'class'=>'form-control',
				'style' => 'display: inline;',
				'value' => $from_date
			));
			echo $this->Form->input('to_date', array(
				'type' => 'date',
				'dateFormat' => 'YMD',
				'monthNames' => false,
				'timeFormat' => '24',
				'minYear' => date('Y') - 5,
				'maxYear' => date('Y'),
				'separator' => ' / ',
				'label'=> '～',
				'class'=>'form-control',
				'style' => 'display: inline;',
				'value' => $to_date
			));
			echo '</div>';
			echo $this->Form->end();
		?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
		<th><?php echo $this->Paginator->sort('course_id', 'コース'); ?></th>
		<th><?php echo $this->Paginator->sort('content_id', 'コンテンツ'); ?></th>
		<th><?php echo $this->Paginator->sort('user_id', '氏名'); ?></th>
		<th class="ib-col-center"><?php echo $this->Paginator->sort('score', '得点'); ?></th>
		<th class="ib-col-center" nowrap><?php echo $this->Paginator->sort('pass_score', '合格点'); ?></th>
		<th class="ib-col-center"><?php echo $this->Paginator->sort('is_passed', '結果'); ?></th>
		<th class="ib-col-center"><?php echo $this->Paginator->sort('is_complete', '完了'); ?></th>
		<th class="ib-col-center" nowrap><?php echo $this->Paginator->sort('understanding', '理解度'); ?></th>
		<th class="ib-col-center"><?php echo $this->Paginator->sort('study_sec', '学習時間'); ?></th>
		<th class="ib-col-datetime"><?php echo $this->Paginator->sort('created', '学習日時'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($records as $record): ?>
	<tr>
		<td><a href="javascript:openRecord(<?php echo h($record['Course']['id']); ?>, <?php echo h($record['User']['id']); ?>);"><?php echo h($record['Course']['title']); ?></a></td>
		<td><?php echo h($record['Content']['title']); ?>&nbsp;</td>
		<td><?php echo h($record['User']['name']); ?>&nbsp;</td>
		<td class="ib-col-center"><?php echo h($record['Record']['score']); ?>&nbsp;</td>
		<td class="ib-col-center"><?php echo h($record['Record']['pass_score']); ?>&nbsp;</td>
		<td class="ib-col-center"><?php echo h(Configure::read('record_result.'.$record['Record']['is_passed'])); ?>&nbsp;</td>
		<td class="ib-col-center"><?php echo h(Configure::read('record_complete.'.$record['Record']['is_complete'])); ?>&nbsp;</td>
		<td class="ib-col-center"><?php echo h(Configure::read('record_understanding.'.$record['Record']['understanding'])); ?>&nbsp;</td>
		<td class="ib-col-center"><?php echo h(Utils::getHNSBySec($record['Record']['study_sec'])); ?>&nbsp;</td>
		<td class="ib-col-date"><?php echo h(Utils::getYMDHN($record['Record']['created'])); ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<?php echo $this->element('paging');?>
</div>
