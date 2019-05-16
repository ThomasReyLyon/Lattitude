
$("#picture").fileinput({
    theme: 'fas',
    uploadUrl: '#', // you must set a valid URL here else you will get an error
    allowedFileExtensions: ['jpg', 'png', 'gif'],
    maxFileSize: 500000,
    maxFilesNum: 3,
    allowedFileTypes: ['image', 'video', 'flash'],
    slugCallback: function (filename) {
        return filename.replace('(', '_').replace(']', '_');
    }
});

