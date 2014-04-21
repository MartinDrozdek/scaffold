<?php //netteCache[01]000386a:2:{s:4:"time";s:21:"0.97561000 1398112052";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:72:"C:\webserver\apache\htdocs\bc2\nette-blog\app\templates\Kolo2\view.latte";i:2;i:1398111720;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:22:"released on 2014-03-17";}}}?><?php

// source file: C:\webserver\apache\htdocs\bc2\nette-blog\app\templates\Kolo2\view.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, '2bnbvpdxww')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb449921415b_content')) { function _lb449921415b_content($_l, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><p><a href="<?php echo htmlSpecialChars($_control->link("Kolo2:default")) ?>">← zpět na výpis příspěvků</a></p>

<h2>Detail záznamu</h2>

<table>
    <tr>
       <td>
          Size
       </td>
       <td>
          <?php echo Nette\Templating\Helpers::escapeHtml($item->size, ENT_NOQUOTES) ?>

       </td>
    </tr>
    <tr>
       <td>
          Company
       </td>
       <td>
          <?php echo Nette\Templating\Helpers::escapeHtml($item->company, ENT_NOQUOTES) ?>

       </td>
    </tr>
</table>

<?php
}}

//
// end of blocks
//

// template extending and snippets support

$_l->extends = empty($template->_extended) && isset($_control) && $_control instanceof Nette\Application\UI\Presenter ? $_control->findLayoutTemplateFile() : NULL; $template->_extended = $_extended = TRUE;


if ($_l->extends) {
	ob_start();

} elseif (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
?>

<?php if ($_l->extends) { ob_end_clean(); return Nette\Latte\Macros\CoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render(); }
call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()) ; 