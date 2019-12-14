<?php

namespace DenisKisel\Repeator;

use Encore\Admin\Admin;
use Encore\Admin\Form;
use Encore\Admin\Form\Field;

class Repeator extends Field
{
    protected $builder = null;
    protected $column = null;


    public function __construct($column, array $arguments = [])
    {
        $this->column = $column;

        if (count($arguments) == 1) {
            $this->label = $this->formatLabel();
            $this->builder = $arguments[0];
        }

//        parent::__construct($arrayField, $arguments);
    }

    /**
     * Render the `HasMany` field.
     *
     * @throws \Exception
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // specify a view to render.
        $this->view = 'dkrepeator::repeator';

        $this->buildNestedForm($this->column, $this->builder);

        list($template, $script) = $this->buildNestedForm($this->column, $this->builder)
            ->getTemplateHtmlAndScript();

//
        $this->setupScriptForDefaultView($script);

        return parent::render()->with([
            'forms'        => $this->buildRelatedForms(),
            'template'     => $template,
            'relationName' => null,
        ]);
    }

    protected function buildNestedForm($arrayField, \Closure $builder, $key = null)
    {
        $form = new Form\NestedForm($arrayField, $key);
        $form->setForm($this->form);
        call_user_func($builder, $form);
        return $form;
    }

    protected function buildRelatedForms()
    {
        $forms = [];

        if ($values = old($this->column)) {
            foreach ($values as $key => $data) {
                $forms[$key] = $this->buildNestedForm($this->column, $this->builder, $key)
                    ->fill($data);
            }
        } elseif ($this->value) {
            foreach ($this->value as $key => $data) {
                $forms[$key] = $this->buildNestedForm($this->column, $this->builder, $key)
                    ->fill($data);
            }
        }

        return $forms;
    }

    protected function setupScriptForDefaultView($templateScript)
    {
        $removeClass = Form\NestedForm::REMOVE_FLAG_CLASS;
        $defaultKey = Form\NestedForm::DEFAULT_KEY_NAME;

        /**
         * When add a new sub form, replace all element key in new sub form.
         *
         * @example comments[new___key__][title]  => comments[new_{index}][title]
         *
         * {count} is increment number of current sub form count.
         */
        $script = <<<EOT

function NFsetKeys() {
    if ($('.has-many-items-form').length > 0) {
        $('.has-many-items-form').each(function (i, v) {
            $(v).find('*[name]').each(function (inputI, inputValue) {
                $(inputValue).attr('name', $(inputValue).attr('name').replace(/new___LA_KEY__/g, i));
                $(inputValue).attr('name', $(inputValue).attr('name').replace(/NaN/g, i));
            })
        });
    }
}

var {$this->column}_index = $('.has-many-items-form').length;
var attrName = $('.has-many-{$this->column}-form:last-of-type input').attr('name');
if ($('.has-many-{$this->column}-form:last-of-type input').length != 0) {
    var match = attrName.match(/new_(\d)*/);
    {$this->column}_index = Number(match[1]);
}

$('#has-many-{$this->column}').on('click', '.add', function () {

    var tpl = $('template.{$this->column}-tpl');

    {$this->column}_index++;

    var template = tpl.html().replace(/{$defaultKey}/g, {$this->column}_index);
    $('.has-many-{$this->column}-forms').append(template);
    NFsetKeys();
    {$templateScript}
});

$('#has-many-{$this->column}').on('click', '.remove', function () {
    $(this).closest('.has-many-{$this->column}-form').remove();
    $(this).closest('.has-many-{$this->column}-form').find('.$removeClass').val(1);
});

NFsetKeys();

EOT;

        Admin::script($script);
    }

}
