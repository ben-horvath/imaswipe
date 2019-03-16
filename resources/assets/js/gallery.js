var config = {
    supportedMIMETypes: {
        images: [
            'image/gif',
            'image/jpeg',
            'image/png'
        ],
        videos: [
            'video/mp4',
            'video/webm',
            'video/ogg'
        ]
    },
    bufferSizeMax: 10,
    defaultMode: 'browse',
    syncInterval: 1100
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
        if (config.supportedMIMETypes.images.includes(mimeType)) {
            return 'image';
        } else if (config.supportedMIMETypes.videos.includes(mimeType)) {
            return 'video';
        }
    }
}

class ImageMedium extends Medium {
    constructor(id = null, name = null, extension = null, mimeType = null, src = null) {
        super(id, name, extension, mimeType, src);

        this.image = new Image();
    }

    load() {
        if (typeof this.src === 'string' && this.src.length) {
            this.image.src = this.src;
        }
    }
}

class VideoMedium extends Medium {
    constructor(id = null, name = null, extension = null, mimeType = null, src = null) {
        super(id, name, extension, mimeType, src);

        this.video = document.createElement('video');
    }

    load() {
        if (typeof this.src === 'string' && this.src.length) {
            this.video.src = this.src;
        }
    }
}

class Request {
    constructor(mode = config.defaultMode, startWith = null) {
        this.mode = mode;
        this.startWith = startWith;
        this.delete = [];
        this.approve = [];
    }
}

class MediaBuffer {
    constructor(mode = config.defaultMode, startWith = null, bufferSize = config.bufferSizeMax) {
        this.mode = mode;
        if (
            bufferSize >= 0 &&
            bufferSize < config.bufferSizeMax
        ) {
            this.bufferSize = Math.floor(bufferSize);
        } else {
            this.bufferSize = config.bufferSizeMax;
        }
        this.media = [];
        this.status = 'empty';
        this.request = new Request(mode, startWith);
        this.syncTimer = setInterval(this.syncWithServer.bind(this), config.syncInterval);
    }

    syncWithServer() {
        var buffer = this;
        axios.post('/api/media/sync', buffer.request)
            .then(response => {
                buffer.fillBuffer(response);
            })
            .catch(response => {
                console.log(response); // TODO: error handling
            })
            .then(() => {
                // Always executed
                if (buffer.media.length) {
                    buffer.status = 'OK';
                } else {
                    buffer.status = 'empty';
                }
            });
        buffer.request = new Request(buffer.mode);
    }

    fillBuffer(response) {
        if (
            !response.hasOwnProperty('data') ||
            !response.data.hasOwnProperty('data') ||
            response.data.data.legth < 1
        ) {
            this.status = 'endOfSupply';
            return;
        }

        walkThroughNew:
        for (
            let i = 0;
            this.media.length < this.bufferSize &&
            i < response.data.data.length;
            i++
        ) {
            for (let j = 0; j < this.media.length; j++) {
                if (this.media[j].name == response.data.data[i].name) {
                    continue walkThroughNew;
                }
            }

            let relevantData = [
                response.data.data[i].id,
                response.data.data[i].name,
                response.data.data[i].extension,
                response.data.data[i].mime_type,
                response.data.data[i].url
            ];
            
            let type = Medium.getType(response.data.data[i].mime_type);
            
            if (typeof type !== 'string') {
                console.log('Not supported MIME type: ' + typeof type); // TODO: error handling
                return;
            }

            switch (type) {
                case 'image':
                    this.media.push(new ImageMedium(...relevantData));
                    this.media[this.media.length - 1].load();
                    break;
            
                case 'video':
                    this.media.push(new VideoMedium(...relevantData));
                    this.media[this.media.length - 1].load();
                    break;
            
                default:
                    console.log('Unsupported medium type: ' + type); // TODO: error handling
                    break;
            }
        }
    }

    get medium() {
        if (this.media.length) {
            return this.media[0];
        } else {
            return new Medium();
        }
    }

    stepMedia() {
        if (this.media.length) {
            this.media.shift();
        }
    }

    deleteMedium() {
        if (this.media.length) {
            this.request.delete.push(this.media[0].name);
            this.media.shift();
        }
    }

    approveMedium() {
        if (this.media.length) {
            this.request.approve.push(this.media[0].name);
            this.media.shift();
        }
    }
}

export {
    supportedMIMETypes,
    Medium,
    ImageMedium,
    VideoMedium,
    MediaBuffer
}
