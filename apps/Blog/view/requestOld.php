<?
extract($this->context);
?>

<header />

<div class="FW">
    <div class="CW">
        <?
        foreach($blog as $b){
            $avatar = $b->admin->avatar->image->uri;
            $name = $b->admin->name;
            $date = date('d.m.y',$b->created);
            $txt = mb_substr($b->text,0,200);
            echo "
                    <div class='contentmain bloglist'>
                        <div class='FL bloglistitem'>
                            <a class='FL bloglistitemAv' style='background: url($avatar) 50% 50% no-repeat;background-size: cover;'></a>
                            <div class='FL bloglistitemDiv'>
                                <a>$name</a>
                                <div>$date</div>
                            </div>
                            <br class='clear'>
                        </div>
                        <div class='FL bloglistitemL'>
                            <a href='/blog/$b->id'>$b->title</a>
                            <a href='/blog/$b->id'>
                                {$txt}...
                            </a>
                        </div>
                        <br class='clear'>
                    </div>
                 ";
        }
        ?>


    </div>
</div>

<footer />
