$(function () {

    // open in new window
    $('.openInWindow').on("click",function(e){
        var url = $(this).attr("href");
        var windowName = "popUp";//$(this).attr("name");
        var windowSize = "width=650,height=400,scrollbars=yes";

        window.open(url, windowName, windowSize);

        event.preventDefault();
    });

    var focusedDiv = "";
    var flash = $('#flash');

    if (flash.is(':visible')) {
        flash.setRemoveTimeout(5000);
    }

    $('.verticalFeedList').each(function(e){
        $(this).perfectScrollbar({
            "wheelSpeed" : 50
        });
    });

    $('#youtubers').perfectScrollbar({
        "wheelSpeed" : 50,
        "suppressScrollY" : true
    });

    var isScrolled = false;
    $('.module ul.expandable').on("scroll",function(e){
        var list = $(this);
       if(list.scrollTop() === list.prop('scrollHeight') - list.height()){
           if(!isScrolled){
               isScrolled = true;
               var category = list.attr('id');
               var page = parseInt(list.attr('data-page')) + 1;
               var total = list.attr('data-total');
               if(page-1 >= total){
                   var limitDiv = list.children('.limit-reached');
                    if(limitDiv.length != 0){
                        alert('here')
                        console.log(limitDiv);
                        return;
                    }else{
                        limitDiv = $("<li />",{
                            "class" : "limit-reached",
                            "text" : "There are no more feeds available."
                        }).appendTo(list);
                    }
               }else{
                   list.toggleLoadingImage();
                   $.ajax({
                       url: baseUrl + '/get-more-feeds/' + category + '/' + page,
                       dataType: "html"
                   }).success(function (data) {
                       isScrolled = false;
                       list.append(data).perfectScrollbar("update").toggleLoadingImage();

                   });
                   list.attr('data-page', page);
               }
           }
       }
    });

    var isSearchingYoutuberFeeds = false;
    $('#youtubers li').on("click",function(e){
           var liItem = $(this);
        delay(function () {
           if(liItem.hasClass("active") || isSearchingYoutuberFeeds) return;
            isSearchingYoutuberFeeds = true;
            liItem.addClass("active");
            liItem.siblings(".active").removeClass("active");
            var feedsList = $('#youtubersFeeds');
            var module = feedsList.parent();
            var youtuberSpan = liItem.find('span[class^="youtuber"]');
            var youtuberName = youtuberSpan.attr('class').substring(9);
            feedsList.slideUp();
            module.toggleLoadingImage();
            $.ajax({
                url: baseUrl + '/get-youtuber-feeds/' + youtuberName,
                timeout: 5000
            }).done(function (data) {
                feedsList.html('').append(data).slideDown();
                feedsList.siblings('h1').html(youtuberSpan.html() + "'s Latest Feeds");
                module.toggleLoadingImage();
                isSearchingYoutuberFeeds = false;
                feedsList.perfectScrollbar("destroy");
                feedsList.perfectScrollbar();
            }).fail(function (jqXHR, textStatus) {
                if (textStatus == 'timeout') {
                    alert('The search has timed out, please try again.');
                } else {
                    addMessage('Something with wrong with the game search, please try again.');
                }
            });
        }, 150);
    });

    $('.hideToggle').on('click',function(e){
        var btn = $(this);
        var list = btn.siblings('ul');
        console.log(list);
        if(list.is(":visible")){
            list.slideUp();
            btn.html("Show");
        }else{
            list.slideDown();
            btn.html("Hide");
        }
    });

    // if the shadow div exists, when you click outside of the
    // focused box it should be reverted to normal
    $(document).on('click', function (event) {
        if (event.target.id == 'shadow') {
            focusedDiv.unfocusLight();

            $('body').removeClass('unscrollable');
            if (focusedDiv.attr('id') == 'stage') {
                focusedDiv.find('iframe').detach();
                focusedDiv.hide();
            } else {
                focusedDiv.detach();
            }
        }
    });

    $(document).on('click', '#rating a',function(e){
        e.preventDefault();
        var btn = $(this);
        var otherBtn = btn.siblings('a[id^="thumb"]');
        if (!btn.hasClass('disabled')) {
            btn.addClass('disabled');
            var rating = btn.attr("id");
            var feed = $("#feed");
            var feedId = feed.attr('class').substring(5);

            $.ajax({
                url: baseUrl + '/feed/rate/' + rating + '/id/' + feedId,
                timeout: 5000
            }).success(function (data) {
                if (data.success == 1){
                    var totalSpan = $("#total .count");
                    var total = parseInt(totalSpan.html());
                    if(otherBtn.hasClass("disabled")){
                        otherBtn.removeClass('disabled');
                        total += (rating == "thumbUp") ? 2 : -2;
                    }else{
                        total += (rating == "thumbUp") ? 1 : -1;
                    }
                    totalSpan.html(total);
                }else{
                    addMessage(data.message);
                }
            });

        }
    });

//    /**
//     * @description Creates the stage and plays a video.
//     */
//    $(document).on('click', '#history li a', function (e) {
//        e.preventDefault();
//        e.stopPropagation();
//        var playBtn = $(this);
//        var listItem = playBtn.closest('li');
//        var feedId = listItem.attr('class').substring(5);
//        toggleVideo('hide');
//        var stageWrapper = $('<div/>', {
//            'id': "stageWrapper"
//        }).prependTo($('body'));
//
//        var stage = $('<div/>', {
//            'id': 'stage'
//        }).appendTo(stageWrapper);
//
//        var videoWrapper = $('<div/>', {
//            'id': 'videoWrapper'
//        }).appendTo(stage);
//
//        var videoPlayer = $('<iframe/>', {
//            "id": "youtubeVideo",
//            "frameborder": 0,
//            "src": "http://www.youtube.com/embed/" + feedId + "?autoplay=1"
//        }).appendTo(videoWrapper);
//
//        focusedDiv = stageWrapper;
//        stageWrapper.addClass('target-' + feedId).focusLight();
//        $('body').addClass('unscrollable');
//    });

});
