<?php
include("header.inc.php");
HTMLBegin();
?>
<div class="container">

    <div class="card">
        <div class="card-header" style="text-align: center"><?php echo C_LOOKUP_PAGE_HELP_PAGE_HEADER ?></div>
        <div class="card-body">
            <?php echo C_LOOKUP_PAGE_HELP_PAGE_CONTENT ?>
		<pre>
        function SetPerson(PersonID, PersonName)
        {
            document.<\? echo $_REQUEST["FormName"]; >.<\? echo $_REQUEST["InputName"]; >.value=PersonID;
            window.opener.document.getElementById(<\? echo $_REQUEST["SpanName"]; >).innerHTML=PersonName;
            window.close();
        }
		</pre>

        </div>
    </div>
</div>