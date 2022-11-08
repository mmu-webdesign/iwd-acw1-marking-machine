// worst to best 
var htmlexercises = [
    "Not present or not participated.",
    "",
    "",
    "Major errors or omissions.",
    "",
    "Minor errors or omissions.",
    "",
    "Exercises are completed accurately."
];

// worst to best
var cssexercises = [
    "Not present or not participated.",
    "",
    "",
    "Major errors or omissions.",
    "",
    "Minor errors or omissions.",
    "",
    "Exercises are completed accurately."
];

// worst to best
var imageoptimisation = [
    "Not present or not participated.",
    "",
    "",
    "Major errors or omissions.",
    "",
    "Minor errors or omissions.",
    "",
    "Exercises are completed accurately."
];



// worst to best
var metadata = [
    "Little or no attempt to create an accessible website, or errors that make the site almost totally inaccessible.",
    "Limited or flawed attempt to create an accessible website with unacceptable errors: you may for example have very poor colour contrast, poor or missing alt text.",
    "Partial attempt to create an accessible website with unacceptable errors: you may have very poor colour contrast, poor or missing alt text.",
    "Satisfactory attempt to create an accessible website: you may still have poor colour contrast, poor or missing alt text.",
    "Coherent and careful delivery of an accessible website: you may have some poor colour contrast, poor or missing alt text.",
    "Thorough and precise delivery of an accessible website: you have good colour contrast and your alt text is in place.",
    "Meticulous work that fulfils the required accessibility elements: you have good colour contrast and your alt text is in place. You have gone beyond what is required and added useful extra code or design elements to ensure accessibility.",
    "Meticulous work that fulfils the required accessibility elements: you have excellent colour contrast, your alt text is fit for purpose. You have gone beyond what is required and added useful extra code or design elements to create an accessible, professional website."
];

var working_with_html = [
    "Little or no attempt to implement a responsive design.",
    "Limited or flawed attempt to implement a responsive design.",
    "Partial attempt to fulfil the required responsive design process. Did you work mobile first?",
    "Satisfactory attempt to fulfil the required responsive design process. There are likely to be multiple errors. Did you work mobile first? Check your work in a mobile device.",
    "Coherent and careful delivery of a responsive design. There may be some errors. Your site is mostly responsive but there may be CSS missing in some areas.",
    "Thorough and precise delivery of a responsive design. There may be minor errors. Your site is completely responsive.",
    "Meticulous work that creates a responsive design and shows sophistication. Your site is responsive and works well at all screen sizes.",
    "Professional standard work that creates a responsive design and shows creativity. Your site is responsive and has added thought to create an industry-standard site."
];

var working_with_files = [
    "No attempt to add any JavaScript.",
    "Some code is in place but there are major syntax errors.",
    "Some code is in place but does not work.",
    "Some code is in place but only partly works.",
    "The first code example is in place and works",
    "The second code example is in place and works",
    "All code is in place and works correctly.",
    "All code is in place and works correctly. You have added further code or modified it successfully to fit your website."
];


// worst to best
var accessibility = [
    "Little or no attempt to create an accessible website, or errors that make the site almost totally inaccessible.",
    "Limited or flawed attempt to create an accessible website with unacceptable errors: you may for example have very poor colour contrast, poor or missing alt text.",
    "Partial attempt to create an accessible website with unacceptable errors: you may have very poor colour contrast, poor or missing alt text.",
    "Satisfactory attempt to create an accessible website: you may still have poor colour contrast, poor or missing alt text.",
    "Coherent and careful delivery of an accessible website: you may have some poor colour contrast, poor or missing alt text.",
    "Thorough and precise delivery of an accessible website: you have good colour contrast and your alt text is in place.",
    "Meticulous work that fulfils the required accessibility elements: you have good colour contrast and your alt text is in place. You have gone beyond what is required and added useful extra code or design elements to ensure accessibility.",
    "Meticulous work that fulfils the required accessibility elements: you have excellent colour contrast, your alt text is fit for purpose. You have gone beyond what is required and added useful extra code or design elements to create an accessible, professional website."
];

// worst to best ## Richard - again relating to template, other comments less specific.
var working_with_css = [
    "Little or no attempt to implement the stylesheet provided.",
    "Limited or flawed attempt to implement the stylesheet for this site. There are big gaps in your knowledge of CSS and its syntax.",
    "Partial attempt to implement the stylesheet provided for this site. There are some gaps in your knowledge of CSS and its syntax.",
    "Satisfactory attempt to implement the required styles for this site. There are likely to be multiple errors.",
    "Coherent and careful delivery of the relevant styles for this site. There may be some errors.",
    "Thorough and precise delivery of a relevant stylesheet for this site. There may be minor errors. You have written a valid, organised stylesheet.",
    "Meticulous work creating a stylesheet that shows sophistication. You have written a valid, organised stylesheet and may have added interesting code.",
    "Professional standard work that has produced a stylesheet that shows creativity and best practices. You have written a professional standard stylesheet with cutting-edge code."
];

// worst to best ## Richard - perhaps add art direction to the last one?
var imageimplementation = [
    "No attempt to add images to your page.",
    "Code is in place but images fail to load.",
    "Code is in place, images load but are not optimised for the web.",
    "Code is in place, images load but optimisation is inconsistent.",
    "not used",
    "not used",
    "Code is in place and all images are fully optimised for the web.",
    "Code is in place and all images are fully optimised for the web. You have added further code or modified your images to fit your website."
];

$(document).ready(function () {


    function update_average() {


        var count = 0;
        var min_total = 0;
        var max_total = 0;
        var avg_total = 0;

        $('label.current').each(function () {

            count++;
            var convert = $(this).text();
            var get_numbers = convert.split('-');
            //console.log('convert is '+get_numbers);
            var section_average = Math.round((parseInt(get_numbers[0]) + parseInt(get_numbers[1])) / 2);
            min_total = min_total + parseInt(get_numbers[0]);
            max_total = max_total + parseInt(get_numbers[1]);
            avg_total = avg_total + section_average;

        });

        var bottom = Math.round((min_total / count));
        var average = Math.round((avg_total / count));
        var top = Math.round((max_total / count));


        $('.average-readout').html(count+' completed of 9');

    }

    function get_message(selected, click_index) {

        var use_array = $(selected).closest('section').attr('id');
        var use_array_key = click_index;

        console.log(use_array);

        var the_message = this[use_array][use_array_key];

        $(selected).closest('section').find('span.autocontent').text(the_message);

    }


    $('ul li label').click(function () {

        $(this).addClass('current').parent().siblings().children().removeClass('current');
        var click_index = $(this).parent().index();

        get_message($(this), click_index);
        update_average();
        checkcomplete();
    });




    function checkcomplete() {

        var totaltocheck = $('.marks').length;
        var finaltocheck = $('.marks-final').length;
        
        console.log(totaltocheck);
        console.log(finaltocheck);

        var currentmarked = $('.marks ul li label.current').length;
        var currentfinalmarked = $('.marks-final ul li label.current').length;

        console.log(currentmarked);
        console.log(currentfinalmarked);
        
        if(totaltocheck == currentmarked){
            updaterelatedreadout();
            
        }
        
        if(finaltocheck == currentfinalmarked){
            updatefinalrelatedreadout();
            
        }
        
        if(totaltocheck == currentmarked && finaltocheck == currentfinalmarked)
        {
            dofinalcalculation();
        }
        

    }

    function updaterelatedreadout(){
        // this needs to get the scores for the first three and put them in the right bits.
        var html_score = $('#htmlexercises label.current').text().split('-');
        var css_score = $('#cssexercises label.current').text().split('-');
        var image_score = $('#imageoptimisation label.current').text().split('-');
        $('.my-mark.html span').text(html_score[1]);
        $('.my-mark.css span').text(css_score[1]);
        $('.my-mark.images span').text(image_score[1]);
   
    }
    
    function updatefinalrelatedreadout(){
        
        
        var count = 0;
        var min_total = 0;
        var max_total = 0;
        var avg_total = 0;

        $('.marks-final label.current').each(function () {

            count++;
            var convert = $(this).text();
            var get_numbers = convert.split('-');
            //console.log('convert is '+get_numbers);
            var section_average = Math.round((parseInt(get_numbers[0]) + parseInt(get_numbers[1])) / 2);
            min_total = min_total + parseInt(get_numbers[0]);
            max_total = max_total + parseInt(get_numbers[1]);
            avg_total = avg_total + section_average;

        });

        var bottom = Math.round((min_total / count));
        var average = Math.round((avg_total / count));
        var top = Math.round((max_total / count));
        
        // console.log(bottom);
        // console.log(average);
        // console.log(top);
        
        $('.my-mark.final span').text(top);
        $('.final-overall').text(top);
    }

    function dofinalcalculation(){
        
      var html_score =   $('.my-mark.html span').text();
      var css_score =  $('.my-mark.css span').text();
       var image_score =  $('.my-mark.images span').text();
       var final_score = $('.my-mark.final span').text();
       
       console.log(html_score);
       console.log(css_score);
       console.log(image_score);
       console.log(final_score);
       
       var final_mark = Math.round((html_score*0.225)+(css_score*0.225)+(image_score*0.05)+(final_score*0.5));
       
     $('.my-mark.all span').text(final_mark);
       
     $('html').addClass('success');
        
    }
    
    
    checkcomplete();



});