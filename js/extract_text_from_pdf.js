var datass = '';
var DataArr = [];
PDFJS.workerSrc = '';



function extract_text_from_pdf(file, options = { isUrl: false }) {
    // var input = document.getElementById("file-id");
    return new Promise((final_resolve, final_reject) => {





        // var url = "http://localhost:7000/cms-for-personal-group/upload/ds/unit-6/note/syllabus_big%20data%20hadoop.pdf";
        // Asynchronous download PDF
        if (options.isUrl) {
            pdfAsArray(file)
        } else {
            ExtractText(file)
        }

        // .then(function(pdf) {
        // // return pdf.getPage(1);
        // console.log("**********" )
        // console.log(pdf )
        // 
        // })











        function ExtractText(file) {
            // var input = document.getElementById("file-id");
            var fReader = new FileReader();
            fReader.readAsDataURL(file);
            // console.log(input.files[0]);
            fReader.onloadend = function (event) {
                convertDataURIToBinary(event.target.result);
            }
        }

        var BASE64_MARKER = ';base64,';

        function convertDataURIToBinary(dataURI) {

            var base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
            var base64 = dataURI.substring(base64Index);
            var raw = window.atob(base64);
            var rawLength = raw.length;
            var array = new Uint8Array(new ArrayBuffer(rawLength));

            for (var i = 0; i < rawLength; i++) {
                array[i] = raw.charCodeAt(i);
            }
            pdfAsArray(array)

        }

        function getPageText(pageNum, PDFDocumentInstance) {
            // Return a Promise that is solved once the text of the page is retrieven
            return new Promise(function (resolve, reject) {
                PDFDocumentInstance.getPage(pageNum).then(function (pdfPage) {
                    // The main trick to obtain the text of the PDF page, use the getTextContent method

                    pdfPage.getTextContent().then(function (textContent) {
                        var textItems = textContent.items;
                        var finalString = "";
                        var line = 0;
                        // console.log(textContent)
                        // Concatenate the string of the item to the final string
                        for (var i = 0; i < textItems.length; i++) {
                            if (line != textItems[i].transform[5]) {
                                if (line != 0) {
                                    finalString +='\r\n';
                                    // finalString += '<br>';
                                }

                                line = textItems[i].transform[5]
                            }
                            var item = textItems[i];

                            finalString += item.str;
                        }
                        // console.log(finalString )
                        // console.log("----------" )

                        resolve(finalString);
                        // resolve(finalString);
                        // var node = document.getElementById('out_text');
                        // node.innerHTML = node.innerHTML + create_pdf_page(finalString);
                    })


                    function create_pdf_page(page_txt) {
                        return `  <div class="pdf-page"> ${page_txt}</div>`;
                    }
                    // pdfPage.getTextContent().then(function (textContent) {
                    //     var textItems = textContent.items;
                    //     var finalString = "";

                    //     // Concatenate the string of the item to the final string
                    //     for (var i = 0; i < textItems.length; i++) {
                    //         var item = textItems[i];

                    //         finalString += item.str + " ";
                    //     }

                    //     // Solve promise with the text retrieven from the page
                    //     resolve(finalString);
                    // });
                });
            });
        }

        function pdfAsArray(pdfAsArray) {

            PDFJS.getDocument(pdfAsArray).then(function (pdf) {

                // pdf.loadingTask.promise.then(function (pdf) {
                /*   pdf.getPage(1).then(function (page) {
                      // you can now use *page* here
                      console.log("page")
                      console.log(page)
                      var scale = 1.5;
                      var viewport = page.getViewport({ scale: scale, });
                      console.log(viewport)
                      console.log(viewport)
                      viewport.width =   viewport.height= 500;
                      viewport.width =   viewport.height= 500;
                      viewport.height = 1000;
                      console.log( "viewport")
                      console.log( viewport)
                      // Support HiDPI-screens.
                      var outputScale = window.devicePixelRatio || 1;
                      console.log( "outputScale")
                      console.log( outputScale)
                      var canvas = document.getElementById('the-canvas');
                      var context = canvas.getContext('2d');
    
                      canvas.width = Math.floor(viewport.width * outputScale);
                      canvas.height = Math.floor(viewport.height * outputScale);
                      canvas.style.width = Math.floor(viewport.width) + "px";
                      canvas.style.height = Math.floor(viewport.height) + "px";
    
                      var transform = outputScale !== 1
                          ? [outputScale, 0, 0, outputScale, 0, 0]
                          : null;
    
                      var renderContext = {
                          canvasContext: context,
                          transform: [1, 0, 0, 1, 0, 0],
                          viewport: viewport
                      };
                      page.render(renderContext);
                      console.log( renderContext)
                      console.log( renderContext)
                  }); */

                // });
                var pdfDocument = pdf;
                // Create an array that will contain our promises
                var pagesPromises = [];

                for (var i = 0; i < pdf.pdfInfo.numPages; i++) {
                    // Required to prevent that i is always the total of pages
                    (function (pageNumber) {
                        // Store the promise of getPageText that returns the text of a page
                        pagesPromises.push(getPageText(pageNumber, pdfDocument));
                    })(i + 1);
                }

                // Execute all the promises
                Promise.all(pagesPromises).then(function (pagesText) {

                    // Display text of all the pages in the console
                    // e.g ["Text content page 1", "Text content page 2", "Text content page 3" ... ]
                    // console.log(pagesText); // representing every single page of PDF Document by array indexing
                    // console.log(pagesText.length);
                    final_resolve(pagesText);
                    return
                    var outputStr = "";
                    for (var pageNum = 0; pageNum < pagesText.length; pageNum++) {
                        console.log(pagesText[pageNum]);
                        outputStr = "";
                        outputStr = "<br/><br/>Page " + (pageNum + 1) + " contents <br/> <br/>";

                        var div = document.getElementById('output');

                        div.innerHTML += (outputStr + pagesText[pageNum]);

                    }




                });

            }, function (reason) {
                // PDF loading error
                console.error(reason);
            });
        }












    })


}