<?php
$content = file_get_contents('C:/sununews/resources/views/home.blade.php');
$content = str_replace('Str::limit(strip_tags(', 'substr(strip_tags(', $content);
$content = str_replace('text-senachal-red', 'text-senegal-red', $content);
file_put_contents('C:/sununews/resources/views/home.blade.php', $content);
echo "Fixed\n";
