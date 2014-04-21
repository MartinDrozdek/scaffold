<?php //netteCache[01]000387a:2:{s:4:"time";s:21:"0.91012800 1398113250";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:73:"C:\webserver\apache\htdocs\bc2\nette-blog\app\templates\Pes\default.latte";i:2;i:1398113239;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:22:"released on 2014-03-17";}}}?><?php

// source file: C:\webserver\apache\htdocs\bc2\nette-blog\app\templates\Pes\default.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'ynxkzyiwr1')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lba4a62b856b_content')) { function _lba4a62b856b_content($_l, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><p><a href="<?php echo htmlSpecialChars($_control->link("Pes:add")) ?>">Přidat novou položku</a></p>

<h2>Výpis všech</h2>

<table>
    <tr>
       <td>
          Name
       </td>
       <td>
          Age
       </td>
       <td>
          Company
       </td>
       <td>
          Actions
       </td>
    </tr>
<?php $iterations = 0; foreach ($items as $item) { ?>
        <tr>
          <td>
             <?php echo Nette\Templating\Helpers::escapeHtml($item->name, ENT_NOQUOTES) ?>

          </td>
          <td>
             <?php echo Nette\Templating\Helpers::escapeHtml($item->age, ENT_NOQUOTES) ?>

          </td>
          <td>
             <?php echo Nette\Templating\Helpers::escapeHtml($item->company, ENT_NOQUOTES) ?>

          </td>
          <td>
             <a href="<?php echo htmlSpecialChars($_control->link("Pes:view", array($item->id))) ?>">View</a>
             <a href="<?php echo htmlSpecialChars($_control->link("Pes:edit", array($item->id))) ?>">Edit</a>
             <a href="<?php echo htmlSpecialChars($_control->link("Pes:delete", array($item->id))) ?>">Delete</a>
          </td>
        <tr>
<?php $iterations++; } ?>
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