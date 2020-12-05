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

/* spice/offer.html.twig */
class __TwigTemplate_a7d8d8675735dd1bf5ced515c8ad702f2e00ee40e2360e3721d383c7068e8be5 extends Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "spice/offer.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "spice/offer.html.twig"));

        $this->parent = $this->loadTemplate("base.html.twig", "spice/offer.html.twig", 1);
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
        echo "    ";
        $context["id"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 4, $this->source); })()), "request", [], "any", false, false, false, 4), "get", [0 => "id"], "method", false, false, false, 4);
        // line 5
        echo "    ";
        $context["titleType"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 5, $this->source); })()), "request", [], "any", false, false, false, 5), "get", [0 => "titleType"], "method", false, false, false, 5);
        // line 6
        echo "    <div class=\"container\" style=\"justify-content: center\">
        <div class=\"col-md-4 mx-auto mt-5\" >
            <div class=\"card\" >
                <div class=\"card-body\">
                    <h5 class=\"card-title\">";
        // line 10
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 10, $this->source); })()), "title", [], "any", false, false, false, 10), "html", null, true);
        echo "</h5>
                    <p class=\"card-text\">";
        // line 11
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 11, $this->source); })()), "description", [], "any", false, false, false, 11), "html", null, true);
        echo "</p>
                    <p class=\"card-text\">Type de contrat : ";
        // line 12
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 12, $this->source); })()), "KindContract", [], "any", false, false, false, 12), "title", [], "any", false, false, false, 12), "html", null, true);
        echo "  ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 12, $this->source); })()), "TypesContracts", [], "any", false, false, false, 12), "title", [], "any", false, false, false, 12), "html", null, true);
        echo "</p>
                    <p class=\"card-text\">";
        // line 13
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 13, $this->source); })()), "address", [], "any", false, false, false, 13), "html", null, true);
        echo "</p>
                    <p class=\"card-text\">";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 14, $this->source); })()), "zipCode", [], "any", false, false, false, 14), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 14, $this->source); })()), "city", [], "any", false, false, false, 14), "html", null, true);
        echo "</p>
                    <p class=\"card-text\">Date de mise à jour : ";
        // line 15
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 15, $this->source); })()), "updateDate", [], "any", false, false, false, 15), "d/m/y"), "html", null, true);
        echo "</p>
                    <p class=\"card-text\">Date du post : ";
        // line 16
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 16, $this->source); })()), "creationDate", [], "any", false, false, false, 16), "d/m/y"), "html", null, true);
        echo "</p>

                    <div class=\"d-flex flex-row-reverse bd-highlight\">
                        <div class=\"p-2 bd-highlight\">
                            <a href=\"#/";
        // line 20
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 20, $this->source); })()), "id", [], "any", false, false, false, 20), "html", null, true);
        echo "\" class=\"btn btn-outline-primary mx-auto\">Postuler</a>
                            ";
        // line 21
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_USER")) {
            // line 22
            echo "                                <a href=\"/posts/";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["offer"]) || array_key_exists("offer", $context) ? $context["offer"] : (function () { throw new RuntimeError('Variable "offer" does not exist.', 22, $this->source); })()), "id", [], "any", false, false, false, 22), "html", null, true);
            echo "/update\" class=\"btn btn-outline-primary mx-auto\">Modifier</a>
                            ";
        }
        // line 24
        echo "                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "spice/offer.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  127 => 24,  121 => 22,  119 => 21,  115 => 20,  108 => 16,  104 => 15,  98 => 14,  94 => 13,  88 => 12,  84 => 11,  80 => 10,  74 => 6,  71 => 5,  68 => 4,  58 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends 'base.html.twig' %}

{% block body %}
    {% set id = app.request.get('id') %}
    {% set titleType = app.request.get('titleType') %}
    <div class=\"container\" style=\"justify-content: center\">
        <div class=\"col-md-4 mx-auto mt-5\" >
            <div class=\"card\" >
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
                            <a href=\"#/{{offer.id}}\" class=\"btn btn-outline-primary mx-auto\">Postuler</a>
                            {% if is_granted('ROLE_USER') %}
                                <a href=\"/posts/{{offer.id}}/update\" class=\"btn btn-outline-primary mx-auto\">Modifier</a>
                            {% endif %}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}", "spice/offer.html.twig", "/Users/leaduvigneau/Documents/ynov/cours/b2/php/symfony_project/SpiceGirls/templates/spice/offer.html.twig");
    }
}
