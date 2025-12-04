<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* modules/contrib/webform_booking/templates/input--webform-booking.html.twig */
class __TwigTemplate_4c0be3abf58dc7eeeb17a8c0f79a5b1a extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->extensions[SandboxExtension::class];
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 13
        $context["element_id"] = (($_v0 = ($context["element"] ?? null)) && is_array($_v0) || $_v0 instanceof ArrayAccess && in_array($_v0::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v0["#name"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "#name", [], "array", false, false, true, 13));
        // line 14
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "error_message", [], "any", false, false, true, 14)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 15
            yield "  <div class=\"webform-booking-error messages messages--error\">
    ";
            // line 16
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["element"] ?? null), "error_message", [], "any", false, false, true, 16), "html", null, true);
            yield "
  </div>
";
        }
        // line 19
        yield "  <input";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "setAttribute", ["id", ("selected-slot-" . ($context["element_id"] ?? null))], "method", false, false, true, 19), "setAttribute", ["name", (        // line 20
($context["element_id"] ?? null) . "[slot]")], "method", false, false, true, 19), "setAttribute", ["type", "text"], "method", false, false, true, 20), "html", null, true);
        // line 21
        yield " />
<div id=\"appointment-wrapper-";
        // line 22
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["element_id"] ?? null), "html", null, true);
        yield "\">

  <div id=\"calendar-container-";
        // line 24
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["element_id"] ?? null), "html", null, true);
        yield "\"></div>
  <div id=\"slots-container-";
        // line 25
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["element_id"] ?? null), "html", null, true);
        yield "\"></div>
  <div id=\"seats-dropdown-container-";
        // line 26
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["element_id"] ?? null), "html", null, true);
        yield "\"></div>
  <div id=\"price-display-";
        // line 27
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["element_id"] ?? null), "html", null, true);
        yield "\"></div>
</div>

";
        // line 31
        yield "<input type=\"hidden\" id=\"seats-";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["element_id"] ?? null), "html", null, true);
        yield "\" name=\"";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["element_id"] ?? null), "html", null, true);
        yield "[seats]\" value=\"1\" />

";
        // line 33
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["children"] ?? null), "html", null, true);
        yield "
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["element", "attributes", "children"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "modules/contrib/webform_booking/templates/input--webform-booking.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  95 => 33,  87 => 31,  81 => 27,  77 => 26,  73 => 25,  69 => 24,  64 => 22,  61 => 21,  59 => 20,  57 => 19,  51 => 16,  48 => 15,  46 => 14,  44 => 13,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "modules/contrib/webform_booking/templates/input--webform-booking.html.twig", "/var/www/html/web/modules/contrib/webform_booking/templates/input--webform-booking.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 13, "if" => 14];
        static $filters = ["escape" => 16];
        static $functions = [];

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['escape'],
                [],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
