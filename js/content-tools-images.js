/* Yii 2 ContentTools images engine */
function imageUploader(dialog) {
    var image, xhr, xhrComplete, xhrProgress;

    function rotateImage(direction) {
        var formData;

        xhrComplete = function (event) {
            var response;
            if (parseInt(event.target.readyState) !== 4) return;

            xhr = null;
            xhrComplete = null;
            dialog.busy(false);

            if (parseInt(event.target.status) === 200) {
                response = JSON.parse(event.target.responseText);
                if (response.errors) {
                    for (var k in response.errors) console.log(response.errors[k]);
                    new ContentTools.FlashUI('no');
                }
                else {
                    image = {
                        size: response.size,
                        url: response.url + '?_ignore=' + Date.now()
                    };
                    dialog.populate(image.url, image.size);
                }
            } else {
                new ContentTools.FlashUI('no');
            }
        };

        dialog.busy(true);

        formData = new FormData();
        formData.append('url', image.url);
        formData.append('direction', direction);
        if (_CTCSRF.length) {   
            formData.append(_CTCSRF[0], _CTCSRF[1]);
        }
        else {
            console.log('_CTCSRF is not set!');
        }

        if (_CTImagesUrl.length) {
            xhr = new XMLHttpRequest();
            xhr.addEventListener('readystatechange', xhrComplete);
            xhr.open('POST', _CTImagesUrl[1], true);
            xhr.send(formData);
        }
        else {
            console.log('_CTImagesUrl is not set!');
        }
    }
    
    dialog.addEventListener('imageuploader.cancelupload', function () {
        if (xhr) {
            xhr.upload.removeEventListener('progress', xhrProgress);
            xhr.removeEventListener('readystatechange', xhrComplete);
            xhr.abort();
        }
        dialog.state('empty');
    });
    
    dialog.addEventListener('imageuploader.clear', function () {
        dialog.clear();
        image = null;
    });
    
    dialog.addEventListener('imageuploader.fileready', function (event) {
        var formData;
        var file = event.detail().file;

        xhrProgress = function (event) {
            dialog.progress((event.loaded / event.total) * 100);
        };
        xhrComplete = function (event) {
            var response;
            if (parseInt(event.target.readyState) !== 4) return;

            xhr = null;
            xhrProgress = null;
            xhrComplete = null;

            if (parseInt(event.target.status) === 200) {
                response = JSON.parse(event.target.responseText);
                if (response.errors) {
                    for (var k in response.errors) console.log(response.errors[k]);
                    new ContentTools.FlashUI('no');
                }
                else {
                    image = {
                        size: response.size,
                        url: response.url
                    };
                    dialog.populate(image.url, image.size);
                }
            } else {
                new ContentTools.FlashUI('no');
            }
        };

        dialog.state('uploading');
        dialog.progress(0);

        formData = new FormData();
        formData.append('image', file);
        if (_CTCSRF.length) {
            formData.append(_CTCSRF[0], _CTCSRF[1])
        }
        else {
            console.log('_CTCSRF is not set!');
        }
        if (_CTImagesUrl.length) {
            xhr = new XMLHttpRequest();
            xhr.upload.addEventListener('progress', xhrProgress);
            xhr.addEventListener('readystatechange', xhrComplete);
            xhr.open('POST', _CTImagesUrl[0], true);
            xhr.send(formData);
        }
        else {
            console.log('_CTImagesUrl is not set!');
        }
    });
    
    dialog.addEventListener('imageuploader.rotateccw', function () {
        rotateImage('CCW');
    });

    dialog.addEventListener('imageuploader.rotatecw', function () {
        rotateImage('CW');
    });
    
    dialog.addEventListener('imageuploader.save', function () {
        var crop, cropRegion, formData;

        xhrComplete = function (event) {
            if (parseInt(event.target.readyState) !== 4) return;

            xhr = null;
            xhrComplete = null;

            dialog.busy(false);

            if (parseInt(event.target.status) === 200) {
                var response = JSON.parse(event.target.responseText);
                if (response.errors) {
                    for (var k in response.errors) console.log(response.errors[k]);
                    new ContentTools.FlashUI('no');
                }
                else {
                    dialog.save(
                        response.url + '?_ignore=' + Date.now(),
                        response.size,
                        {
                            'alt': response.alt,
                            'data-ce-max-width': image.size[0]
                        }
                    );
                }
            } else {
                new ContentTools.FlashUI('no');
            }
        };

        dialog.busy(true);

        formData = new FormData();
        formData.append('url', image.url);

        formData.append('width', 600);
        if (dialog.cropRegion()) {
            formData.append('crop', dialog.cropRegion());
        }
        if (_CTCSRF.length) {
            formData.append(_CTCSRF[0], _CTCSRF[1])
        }
        else {
            console.log('_CTCSRF is not set!');
        }
        if (_CTImagesUrl.length) {
            xhr = new XMLHttpRequest();
            xhr.addEventListener('readystatechange', xhrComplete);
            xhr.open('POST', _CTImagesUrl[2], true);
            xhr.send(formData);
        }
        else {
            console.log('_CTImagesUrl is not set!');
        }
    });
}
ContentTools.IMAGE_UPLOADER = imageUploader;
