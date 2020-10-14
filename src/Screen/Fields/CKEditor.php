<?php

declare(strict_types=1);

namespace Orchid\Screen\Fields;

use Orchid\Screen\Field;

/**
 * Class CKEditor.
 *
 * @method CKEditor autofocus($value = true)
 * @method CKEditor disabled($value = true)
 * @method CKEditor form($value = true)
 * @method CKEditor formaction($value = true)
 * @method CKEditor formenctype($value = true)
 * @method CKEditor formmethod($value = true)
 * @method CKEditor formnovalidate($value = true)
 * @method CKEditor formtarget($value = true)
 * @method CKEditor name(string $value = null)
 * @method CKEditor placeholder(string $value = null)
 * @method CKEditor readonly($value = true)
 * @method CKEditor required(bool $value = true)
 * @method CKEditor tabindex($value = true)
 * @method CKEditor value($value = true)
 * @method CKEditor help(string $value = null)
 * @method CKEditor height($value = '300px')
 * @method CKEditor title(string $value = null)
 * @method CKEditor popover(string $value = null)
 * @method CKEditor toolbar(array $options)
 */
class CKEditor extends Field
{
    /**
     * @var string
     */
    protected $view = 'platform::fields.ckeditor';

    /**
     * All attributes that are available to the field.
     *
     * @var array
     */
    protected $attributes = [
        'value'   => null,
        'toolbar' => ['text', 'color', 'quote', 'header', 'list', 'format', 'media'],
        'height'  => '300px',
    ];

    public function setLanguage($lang){
      $this->set('language',$lang);
      return $this;
    }

    /**
     * Attributes available for a particular tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'accesskey',
        'autocomplete',
        'autofocus',
        'checked',
        'disabled',
        'form',
        'formaction',
        'formenctype',
        'formmethod',
        'formnovalidate',
        'formtarget',
        'name',
        'placeholder',
        'readonly',
        'required',
        'step',
        'tabindex',
        'value',
        'height',
        'language'
    ];

    /**
     * CKEditor constructor.
     */
    public function __construct()
    {
        $this->addBeforeRender(function () {
            $toolbar = $this->get('toolbar');
            $this->set('toolbar', json_encode($toolbar));
        });
    }
}
