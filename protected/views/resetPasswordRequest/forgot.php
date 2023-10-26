<?php
$this->pageTitle='Forgotten password';
?>
<div class="content">
    <div class="container">
      <?php
        $this->widget('TitleBreadcrumb', [
          'pageTitle' => 'Forgotten password',
          'breadcrumbItems' => [
            ['label' => 'Home', 'href' => '/'],
            ['isActive' => true, 'label' => 'Forgot'],
          ]
        ]);
        ?>
    <div class="subsection row" style="margin-bottom: 130px;">
        <div class="col-xs-12">
            <?php if(Yii::app()->user->hasFlash('fail-reset-password')): ?>
                <div class="alert alert-warning">
                    <?php echo Yii::app()->user->getFlash('fail-reset-password'); ?>
                </div>
            <?php endif; ?>
        </div>
            <div class="reset-message-div">
                <p>Please enter your email. A link to reset your password will be sent to you.</p>
            </div>
            <div class="create-div">
                <? $form=$this->beginWidget('CActiveForm', array(
                    'id'=>'forgot-password-form',
                    'enableAjaxValidation'=>false,
                    'htmlOptions'=>array('class'=>'form-horizontal forgot-password-form')
                )) ?>
                <div class="form-group forgot-password-group">
                    <?php echo $form->label($model, 'email', array('class' => 'col-xs-2 control-label forgot-password-label')); ?>
                    <div class="col-xs-8 forgot-password-input">
                        <?php echo $form->emailField($model, 'email', array('class' => 'form-control', 'required' => 'true')); ?>
                    </div>
                    <div class="col-xs-2">
                        <?= CHtml::submitButton(Yii::t('app' , 'Reset Password'), array('class'=>'btn background-btn forgot-password-btn')) ?>
                    </div>
                </div>
                <? $this->endWidget() ?>
            </div>
        </div>
    </div>
</div>
