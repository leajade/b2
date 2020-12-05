<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* spice/index.html.twig */
class __TwigTemplate_a10a7157b8c6c669b63b655c73dbe2784e4d1edf22a105b4104d8cee3f8247bc extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "spice/index.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "spice/index.html.twig"));

        $this->parent = $this->loadTemplate("base.html.twig", "spice/index.html.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 3
    public function block_body($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 4
        echo "
    <div class=\"container\">
        <div class=\"row\">
            ";
        // line 7
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["offers"]) || array_key_exists("offers", $context) ? $context["offers"] : (function () { throw new RuntimeError('Variable "offers" does not exist.', 7, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["offer"]) {
            // line 8
            echo "                <div class=\"col-md-4 mt-5\">
                    <div class=\"card\">
                        <div class=\"card-body\">
                            <h5 class=\"card-title\">";
            // line 11
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["offer"], "title", [], "any", false, false, false, 11), "html", null, true);
            echo "</h5>
                            <p class=\"card-text\">";
            // line 12
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["offer"], "description", [], "any", false, false, false, 12), "html", null, true);
            echo "</p>
                            <p class=\"card-text\">Type de contrat : ";
            // line 13
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["offer"], "KindContract", [], "any", false, false, false, 13), "title", [], "any", false, false, false, 13), "html", null, true);
            echo "  ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["offer"], "TypesContracts", [], "any", false, false, false, 13), "title", [], "any", false, false, false, 13), "html", null, true);
            echo "</p>
                            <p class=\"card-text\">";
            // line 14
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["offer"], "address", [], "any", false, false, false, 14), "html", null, true);
            echo "</p>
                            <p class=\"card-text\">";
            // line 15
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["offer"], "zipCode", [], "any", false, false, false, 15), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["offer"], "city", [], "any", false, false, false, 15), "html", null, true);
            echo "</p>
                            <p class=\"card-text\">Date de mise à jour : ";
            // line 16
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["offer"], "updateDate", [], "any", false, false, false, 16), "d/m/y"), "html", null, true);
            echo "</p>
                            <p class=\"card-text\">Date du post : ";
            // line 17
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["offer"], "creationDate", [], "any", false, false, false, 17), "d/m/y"), "html", null, true);
            echo "</p>

                            <div class=\"d-flex flex-row-reverse bd-highlight\">
                                <div class=\"p-2 bd-highlight\">
                                    <a href=\"/posts/";
            // line 21
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["offer"], "id", [], "any", false, false, false, 21), "html", null, true);
            echo "\" class=\"btn btn-outline-primary mx-auto\">Allez sur l'offre</a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['offer'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 29
        echo "        </div>
    </div>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "spice/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  131 => 29,  117 => 21,  110 => 17,  106 => 16,  100 => 15,  96 => 14,  90 => 13,  86 => 12,  82 => 11,  77 => 8,  73 => 7,  68 => 4,  58 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends 'base.html.twig' %}

{% block body %}

    <div class=\"container\">
        <div class=\"row\">
            {% for offer in offers %}
                <div class=\"col-md-4 mt-5\">
                    <div class=\"card\">
                        <div class=\"card-body\">
                            <h5 class=\"card-title\">{{ offer.title }}</h5>
                            <p class=\"card-text\">{{ offer.description }}</p>
                            <p class=\"card-text\">Type de contrat : {{ offer.KindContract.title  }}  {{ offer.TypesContracts.title }}</p>
                            <p class=\"card-text\">{{ offer.address }}</p>
                            <p class=\"card-text\">{{ offer.zipCode }} {{ offer.city }}</p>
                            <p class=\"card-text\">Date de mise à jour : {{ offer.updateDate | date(\"d/m/y\") }}</p>
                            <p class=\"card-text\">Date du post : {{ offer.creationDate | date(\"d/m/y\") }}</p>

                            <div class=\"d-flex flex-row-reverse bd-highlight\">
                                <div class=\"p-2 bd-highlight\">
                                    <a href=\"/posts/{{offer.id}}\" class=\"btn btn-outline-primary mx-auto\">Allez sur l'offre</a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>
{% endblock %}", "spice/index.html.twig", "/Users/marie/Documents/ynov_2020_2021/php/symfony_project/SpiceGirls/templates/spice/index.html.twig");
    }
}
