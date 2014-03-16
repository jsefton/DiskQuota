DiskQuota
=========

PHP class used to scan a directory and all sub-directories to give a total 
size of files and summary of usage.

Used to give information about disk quota usage for whole websites hosted 
on VPS's or Local Machines.

=====================================================
Usuage

<code>
$path_name = realpath(dirname(__FILE__));
$maximum_quota = 1000;

$quota = DiskQuota::get_quota($path_name, $maximum_quota);

/*** Returned Array ***/
Array
(
    [directory] => /var/example/folder/website_path/public_html/test_co_uk
    [free_space] => 993.58MB
    [used_space] => 6.42MB
    [total] => 1000MB
    [percentage] => 1
)

/*** Access each item with the below ***/
$free_space = $quota['free_space'];
$used_space = $quota['used_space'];
$percentage_used = $quota['percentage'];
</code>
=====================================================

Other used for percentage can be used for a CSS progress bar by simply making 
a DIV with another DIV inside it. Then setting the inner div to have a width 
of the percentage. This would need to be done on the page itself and not 
within in .css file.

For example.

Example CSS code:
#quota_bar{
    width: 100%;
    height: 20px;
    border: 1px solid #DDD;
}

#quota_bar div{
    width: <?=$percentage_used?>%;
    background-color: #b6f4b9;
    height: 20px;
}

Example HTML code:

<div id="quota_bar"><div></div></div>
