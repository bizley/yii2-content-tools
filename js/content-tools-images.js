var imageUploader = function (dialog) {
    var image, xhr, xhrComplete, xhrProgress;
    var rotateImage = function(direction) {
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
            }
            else new ContentTools.FlashUI('no');
        };
        dialog.busy(true);
        formData = new FormData();
        formData.append('url', image.url);
        formData.append('direction', direction);
        if (_csrf.length) formData.append(_csrf[0], _csrf[1]);
        else console.log('_csrf is not set!');
        if (_imagesUrl.length) {
            xhr = new XMLHttpRequest();
            xhr.addEventListener('readystatechange', xhrComplete);
            xhr.open('POST', _imagesUrl[1], true);
            xhr.send(formData);
        }
        else console.log('_imagesUrl is not set!');
    };

    dialog.bind('imageUploader.cancelUpload', function () {
        if (xhr) {
            xhr.upload.removeEventListener('progress', xhrProgress);
            xhr.removeEventListener('readystatechange', xhrComplete);
            xhr.abort();
        }
        dialog.state('empty');
    });
    dialog.bind('imageUploader.clear', function () {
        dialog.clear();
        image = null;
    });
    dialog.bind('imageUploader.fileReady', function (file) {
        var formData;
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
            }
            else new ContentTools.FlashUI('no');
        };
        dialog.state('uploading');
        dialog.progress(0);
        formData = new FormData();
        formData.append('image', file);
        if (_csrf.length) formData.append(_csrf[0], _csrf[1]);
        else console.log('_csrf is not set!');
        if (_imagesUrl.length) {
            xhr = new XMLHttpRequest();
            xhr.upload.addEventListener('progress', xhrProgress);
            xhr.addEventListener('readystatechange', xhrComplete);
            xhr.open('POST', _imagesUrl[0], true);
            xhr.send(formData);
        }
        else console.log('_imagesUrl is not set!');
    });
    dialog.bind('imageUploader.rotateCCW', function () {
        rotateImage('CCW');
    });
    dialog.bind('imageUploader.rotateCW', function () {
        rotateImage('CW');
    });
    dialog.bind('imageUploader.save', function () {
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
                    dialog.save(
                        response.url + '?_ignore=' + Date.now(),
                        response.size,
                        {
                            'alt': response.alt,
                            'data-ce-max-width': response.size[0]
                        }
                    );
                }
            }
            else new ContentTools.FlashUI('no');
        };
        dialog.busy(true);
        formData = new FormData();
        formData.append('url', image.url);
        if (dialog.cropRegion()) formData.append('crop', dialog.cropRegion());
        if (_csrf.length) formData.append(_csrf[0], _csrf[1]);
        else console.log('_csrf is not set!');
        if (_imagesUrl.length) {
            xhr = new XMLHttpRequest();
            xhr.addEventListener('readystatechange', xhrComplete);
            xhr.open('POST', _imagesUrl[2], true);
            xhr.send(formData);
        }
        else console.log('_imagesUrl is not set!');
    });
};
ContentTools.IMAGE_UPLOADER = imageUploader;