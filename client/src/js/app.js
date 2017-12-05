import jQuery from 'jquery';
import React from 'react';
import ReactDOM from 'react-dom';
import CloudinaryFileField from './components/CloudinaryFileField';
import CloudinaryImageField from './components/CloudinaryImageField';
import CloudinaryVideoField from './components/CloudinaryVideoField';

jQuery.entwine('ss', ($) => {
    $('.madehq-cloudinary-form-file-field').entwine({
        onmatch() {
            console.info('Add Cloudinary File Field', this);
            const data = this.data();
            ReactDOM.render(
              <CloudinaryFileField
                name={data.name}
                fieldType={data.fieldType}
                label={data.label}
                value={data.value}
              />,
              this[0]
            );
        }
    });
    $('.madehq-cloudinary-form-image-field').entwine({
        onmatch() {
            console.info('Add Cloudinary Image Field', this);
            const data = this.data();
            ReactDOM.render(
              <CloudinaryImageField
                name={data.name}
                fieldType={data.fieldType}
                label={data.label}
                value={data.value}
              />,
              this[0]
            );
        }
    });
    $('.madehq-cloudinary-form-video-field').entwine({
        onmatch() {
            console.info('Add Cloudinary Video Field', this);
            const data = this.data();
            ReactDOM.render(
              <CloudinaryVideoField
                name={data.name}
                fieldType={data.fieldType}
                label={data.label}
                value={data.value}
              />,
              this[0]
            );
        }
    });
});
