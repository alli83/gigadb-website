
<section style="margin-bottom:20px;"></section>
<div class="search-box">
    
    <? if (Yii::app()->user->hasFlash('keyword')) { ?>
        <div>
        <?= Yii::app()->user->getFlash('keyword'); ?>
        </div>
    <? } ?>

    <?php echo CHtml::beginForm('/search/new','GET',array('class'=>'search-bar clearfix','onsubmit'=>'return validateForm(this);','role'=>'search')); ?>
    <?php echo CHtml::errorSummary($model); ?>

    
    
    <?php        
               
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name'=>'keyword',
            //'source'=>array('ac1', 'ac2', 'ac3'),
            // 'source'=> array_values($dataset->getListTitles()),
            'source'=> array_values(array()),
            // additional javascript options for the autocomplete plugin
            'options'=>array(
                             'minLength'=>'2',
                             ),
            'htmlOptions'=>array(
                                 'class'=>'search-input'                               
                                 ),
            ));
       
       
    ?>
    <button class="btn-search" type="submit"><span class="fa fa-search"><span class="visually-hidden">Search</span></span></button>
       
   

    <!--
    <a data-toggle="modal" href="#how-to-use-advanced-search" class="hint advanced-search-hint"></a> -->

    <?php echo CHtml::endForm(); ?>
    <span class="fa fa-chevron-circle-left btn-left" title="Previous dataset"></span>
    <span class="fa fa-chevron-circle-right btn-right" title="Next dataset"></span>
    <!--
    <div class="modal hide fade" id="how-to-use-advanced-search">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Using Advanced Search</h3>
      </div>
      <div class="modal-body">
        By default, the boolean mode for keywords is AND. Parentheses may be used for grouping. Other operators include
        <p></p>
        <ul type="disc">


            <li>operator OR: <pre class="programlisting">hello | world</pre></li><li>operator NOT:
<pre class="programlisting">hello -world
hello !world</pre></li>

            <li>field search operator, used for specifying a value a field must have: @ &#60;field name&#62; &#60;value&#62;: <pre class="programlisting">@title hello @body world</pre></li>
            <li>multiple-field search operator: <pre class="programlisting">@(title,body) hello world</pre></li>
            <li>all-field search operator, to get results where any field matches the value: <pre class="programlisting">@* hello</pre></li>
            <li>phrase search operator, to get results where the exact phrase occurs : <pre class="programlisting">"hello world"</pre></li>
            <li>proximity search operator, in the example, a result would match if there are 10 words or less between 'hello' and 'world': <pre class="programlisting">"hello world"~10</pre></li>
            <li>quorum matching operator: , in the example, a result would match if there are any 3 of the 6 keywords: <pre class="programlisting">"the world is a wonderful place"/3</pre></li>
            <li>strict order operator, a result would match if the keywords occur in specified order: <pre class="programlisting">aaa &lt;&lt; bbb &lt;&lt; ccc</pre></li>
            <li>exact form modifier, disables checking for stemming. In the example, there would be no match for 'catch' which stems from 'cat', as does 'cats':<pre class="programlisting">raining =cats and =dogs</pre></li>
            <li>field-start and field-end modifier, will make the keyword match only if it occurred at the very start or the very end of a fulltext field, respectively: <pre class="programlisting">^hello world$</pre></li>
        </ul>
<br/>
<b>Advanced query example:</b>
<br/><br/>
<div class="example-contents"><pre class="programlisting"><b>"hello world" @title "example program"~5 @body python -(php|perl) @* code
</b></pre></div>

<p><br class="example-break">
The full meaning of this search is:

</p>

<div class="itemizedlist"><ul type="disc"><li>Find the words 'hello' and 'world' adjacently in any field in a document;</li><li>Additionally, the same document must also contain the words 'example' and 'program'
    in the title field, with up to, but not including, 10 words between the words in question;
    (E.g. "example PHP program" would be matched however "example script to introduce outside data
    into the correct context for your program" would not because two terms have 10 or more words between them)</li><li>Additionally, the same document must contain the word 'python' in the body field, but not contain either 'php' or 'perl';</li><li>Additionally, the same document must contain the word 'code' in any field.</li></ul></div>

      </div>
      <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">Close</a>
      </div>
    </div>
    -->
 
</div>



<script>
// function submitForm(myform){
//     var strJson= JSON.stringify($(myform).serializeObject());

//     var url=window.location.protocol+"//"+window.location.hostname+(window.location.port ? ':'+window.location.port: '')+$(myform).attr("action");

//     window.location = url+"?criteria=" + strJson;
//     return false;

// }

function validateForm(myform){
    if(myform.keyword.value.length==0) {
        alert("Keyword can not be blank");
        return false;
    }

    return true;

}

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
</script>
