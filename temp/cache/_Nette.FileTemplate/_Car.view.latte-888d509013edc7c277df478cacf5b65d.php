<?php //netteCache[01]000384a:2:{s:4:"time";s:21:"0.47936900 1398112870";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:70:"C:\webserver\apache\htdocs\bc2\nette-blog\app\templates\Car\view.latte";i:2;i:1398112832;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:22:"released on 2014-03-17";}}}?><?php

// source file: C:\webserver\apache\htdocs\bc2\nette-blog\app\templates\Car\view.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, '9ef0orb4eg')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lbfa968dcd5b_content')) { function _lbfa968dcd5b_content($_l, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><p><a href="<?php echo htmlSpecialChars($_control->link("Car:default")) ?>">← zpět na výpis příspěvků</a></p>

<h2>Detail záznamu</h2>

<table>
    <tr>
       <td>
          Company
       </td>
       <td>
          <?php echo Nette\Templating\Helpers::escapeHtml($item->company, ENT_NOQUOTES) ?>

       </td>
    </tr>
    <tr>
       <td>
          Age
       </td>
       <td>
          <?php echo Nette\Templating\Helpers::escapeHtml($item->age, ENT_NOQUOTES) ?>

       </td>
    </tr>
    <tr>
       <td>
          Engine
       </td>
       <td>
          <?php echo Nette\Templating\Helpers::escapeHtml($item->engine, ENT_NOQUOTES) ?>

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