var supportedMIMETypes = {
    'images': [
        'image/gif',
        'image/jpeg',
        'image/png'
    ],
    'videos': [
        'video/webm',
        'video/ogg'
    ]
}

class Medium {
    constructor(id = null, name = null, extension = null, mimeType = null, src = null) {
        this.id = id;
        this.name = name;
        this.extension = extension;
        this.mimeType = mimeType;
        this.src = src;
    }

    static getType(mimeType) {
        if (supportedMIMETypes.images.includes(mimeType)) {
            return 'image';
        } else if (supportedMIMETypes.videos.includes(mimeType)) {
            return 'video';
        }
    }
}

class Image extends Medium {
    constructor(id = null, name = null, extension = null, mimeType = null, src = null) {
        super(id, name, extension, mimeType, src);
    }
}

class Video extends Medium {
    constructor(id = null, name = null, extension = null, mimeType = null, src = null) {
        super(id, name, extension, mimeType, src);
    }
}

export {
    supportedMIMETypes,
    Medium,
    Image,
    Video
}
