<?php
$urlMyform=$this->createUrl('site/ajaxForm');
foreach ($users as $user) {
    echo '<div>';
    echo '<span>ID：' . $user->id . '</span>';
    echo '<span>年龄：' . $user->age . '</span>';
    echo '<span>姓名：' . $user->name . '</span>';
    echo '</div>';
}
?>
<script type="text/javascript" src="http://static.mingyizhudao.com/pc/jquery-1.9.1.min.js"></script>
<div>
    <form id="myform" data-action="<?php echo $urlMyform;?>" method="post">
        <div>
            <input name="user[id]" type="hidden" value="<?php echo rand();?>"/>
        </div>
        <div>
            age: <input name="user[age]" type="text" value="1"/>
        </div>
        <div>
            name:<input name="user[name]" type="text" value="haha"/>
        </div>
        <button id="btnSubmit">submit</button>
    </form>
</div>
<script>
    $(document).ready(function(){
        $("#btnSubmit").click(function(){
            formAjaxSubmit();
        });
        function formAjaxSubmit(){
            var requestUrl=$('#myform').attr('data-action');
            var formdata=$("#myform").serialize();
            $.ajax({
                type:'post',
                url:requestUrl,
                data:formdata,
                success:function(data){
                    console.log(data);
                },
                error:function(){
                   
                }
                
            });
        }
    });
</script>