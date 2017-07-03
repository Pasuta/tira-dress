<?php require dirname(__FILE__).'/../goldcut/boot.php';

define('DEBUG_SQL',TRUE);
//define('PRODUCTION_DB_IN_TEST_ENV',TRUE);
define('NOCLEARDB',true);

class Comment_add implements TestCase
{

    function addComment()
    {
        $m = new Message();
        $m->action = "create";
        $m->urn = "urn-comment";
        $m->user = 'urn-user-123';
        $m->product = 'urn-product-123';
        $m->text = 'teset';
        $m->coworker = 1;

        return $m->deliver();
    }

    function getComment()
    {
        $m = new Message();
        $m->action = "load";
        $m->urn = "urn-comment";
        $result = $m->deliver();
        println($result);
    }
}
?>