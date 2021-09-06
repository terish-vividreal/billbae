  var $modal = $('#profileCropModal');
  var image = document.getElementById('image');
  var cropper;
  $("body").on("change", ".image", function(e){
    var files = e.target.files;
    var done = function (url) {
      image.src = url;
      $modal.modal('open');
    };
    var reader;
    var file;
    var url;
 
    if (files && files.length > 0) {
      file = files[0];
 
      if (URL) {
        done(URL.createObjectURL(file));
      } else if (FileReader) {
        reader = new FileReader();
        reader.onload = function (e) {
          done(reader.result);
        };
        reader.readAsDataURL(file);
      }
    }
  });

  $modal.modal({
      dismissible: true,
      onOpenEnd: function(modal, trigger) { 
        cropper = new Cropper(image, {
        aspectRatio: 15 / 12,
        viewMode: 3,
        preview: '.preview'
      });
      },
      onCloseEnd: function() { 
        cropper.destroy();
        cropper = null;
      } 
    }
  );

  $("#select-files").on("click", function () {
    $("#profile").click();
  })


  