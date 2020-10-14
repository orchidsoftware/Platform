import {Controller} from 'stimulus';

import CKEditor from '@xccjh/xccjh-ckeditor5-video-file-upload/';
import '@xccjh/xccjh-ckeditor5-video-file-upload/build/translations/ru';
import '@xccjh/xccjh-ckeditor5-video-file-upload/build/translations/en';


export default class extends Controller {

  toolbar() {
    return {
      items: [
        'heading',
        '|',
        'bold',
        'italic',
        'link',
        'bulletedList',
        'numberedList',
        '|',
        'indent',
        'outdent',
        '|',
        'imageUpload',
        'blockQuote',
        'insertTable',
        'mediaEmbed',
        'undo',
        'redo',
      ]
    }
  }

  uploadToServer(file, type) {
    return new Promise((resolve, reject) => {
      const formData = new FormData();
      formData.append(type, file);
      axios
        .post(platform.prefix('/systems/files'), formData)
        .then((response) => {
          resolve({ url: response.data.url })
        })
        .catch((error) => {
          reject(response)
          window.platform.alert('Validation error', `Quill ${type} upload failed`);
          console.warn(`quill ${type} upload failed`);
          console.warn(error);
        });
    })
  }

  mediaEmbed() {
    return {
      previewsInData: true,
      extraProviders: [
        {
          name: 'zdy',
          url: [
            /(.*?)/,
          ],
          html: match => {
            const src = match.input;
            return (
              '<div style="position: relative; padding-bottom: 100%; height: 0; padding-bottom: 56.2493%;pointer-events: auto;">' +
              '<video controls style="position: absolute; width: 100%; height: 100%; top: 0; left: 0;" src="' + src + '" autoplay>' +
              '</video>' +
              '</div>'
            );
          }
        },
      ]
    }
  }

  heading() {
    return {
      options: [
        { model: 'paragraph', title: 'paragraph', class: 'ck-heading_paragraph' },
        {
          model: 'heading1',
          view: 'h1',
          title: 'Heading 1',
          class: 'ck-heading_heading1',
        },
        {
          model: 'heading2',
          view: 'h2',
          title: 'Heading 2',
          class: 'ck-heading_heading2',
        },
        {
          model: 'heading3',
          view: 'h3',
          title: 'Heading 3',
          class: 'ck-heading_heading3',
        },
        {
          model: 'heading4',
          view: 'h4',
          title: 'Heading 4',
          class: 'ck-heading_heading4',
        },
      ],
    }
  }

  options(){
    return {
      alignment: {
        options: ['left', 'center', 'right', 'justify'],
      },
      image: {
        toolbar: [
          'imageTextAlternative',
          '|',
          'imageStyle:alignLeft',
          'imageStyle:full',
          'imageStyle:alignRight',
        ],
        resizeUnit: 'px',
        types: ['jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff'],
        styles: ['full', 'alignLeft', 'alignRight'],
      },
      table: {
        contentToolbar: [
          'tableColumn',
          'tableRow',
          'mergeTableCells',
          'tableCellProperties',
          'tableProperties',
        ],
      },
      tableProperties: {
        // ...
      },

      tableCellProperties: {
        // ...
      },
      fontFamily: {
        options: [
          'default',
          'Arial, Helvetica, sans-serif',
          'Courier New, Courier, monospace',
          'Georgia, serif',
          'Lucida Sans Unicode, Lucida Grande, sans-serif',
          'Tahoma, Geneva, sans-serif',
          'Times New Roman, Times, serif',
          'Trebuchet MS, Helvetica, sans-serif',
          'Verdana, Geneva, sans-serif',
        ],
      },
      licenseKey: '',
    }
  }

  connect() {
    const selector = this.element.querySelector('.ckeditor');
    const input = this.element.querySelector('input');
    CKEditor
      .create(selector, {
        language: input.getAttribute('language') || 'en',
        videoUpload: (file) => this.uploadToServer(file, 'video'),
        imageUpload: (file) => this.uploadToServer(file, 'image'),
        mediaEmbed: this.mediaEmbed(),
        heading: this.heading(),
        toolbar: this.toolbar(),
        ...this.options()
      })
      .then(editor => {
        window.editor = editor;
        this.editod = editor;
        editor.setData(input.value)
        editor.ui.view.editable.extendTemplate({
          attributes: {
            style: {
              minHeight: input.getAttribute('height')
            }
          }
        });
        editor.model.document.on('change:data', (evt, data) => {
          input.value = editor.getData()
        });
      })


  }

}
