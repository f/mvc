<?php

/* Index/Text.php */
class __TwigTemplate_a055e6991e07e1eb9e39901d376b6a6d extends Twig_Template
{
    public function display(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        // line 1
        echo "<mobi:page xmlns:mobi=\"http://mobility.pozitim.com\">
\t<mobi:block>
\t\t<?php echo \$only_php; ?> - {\$only_smarty} - {literal}";
        // line 3
        echo twig_escape_filter($this->env, (isset($context['only_twig']) ? $context['only_twig'] : null), "html");
        echo "{/literal}
\t</mobi:block>
</mobi:page>";
    }

    public function getTemplateName()
    {
        return "Index/Text.php";
    }
}
