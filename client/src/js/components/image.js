'use strict';

import Resource from './resource';
import cloudinaryUrl from '../utils/cloudinary-url';

export default class Image extends Resource {
    titleFieldLabel() {
        return 'Caption';
    }

    titleFieldPlaceholder() {
        return 'Provide a friendly caption…';
    }

    descriptionFieldPlaceholder() {
        return "Describe what's in the image for screen readers…";
    }

    thumbnailUrl() {
        const { public_id } = this.props;

        return cloudinaryUrl(public_id, {
            resource_type: 'image',
            width: 200,
            height: 200,
            crop: 'thumb',
            quality: 'auto',
            fetch_format: 'auto',
        });
    }
}