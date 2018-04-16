<!doctype html>
<html>
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo getenv('GOOGLE_ANALYTICS_ID'); ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '<?php echo getenv('GOOGLE_ANALYTICS_ID'); ?>');
  </script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title><?php echo getenv('TOOLKIT_TITLE'); ?></title>

  <meta property="og:url" content="<?php echo getenv('TOOLKIT_URL'); ?>"/>
  <meta property="og:type" content="website"/>
  <meta property="og:title" content="<?php echo getenv('TOOLKIT_TITLE'); ?>"/>
  <meta property="og:description" content="<?php echo getenv('TOOLKIT_DESCRIPTION'); ?>"/>
  <meta name="twitter:title" content="<?php echo getenv('TOOLKIT_TITLE'); ?>">
  <meta name="twitter:description" content="<?php echo getenv('TOOLKIT_DESCRIPTION'); ?>">

  <link href='//fonts.googleapis.com/css?family=Work Sans:400,700,800|Lato:400,300,100,700' rel='stylesheet'
        type='text/css'>
  <link href='./css/volunteer-toolkit.css' rel='stylesheet' type='text/css'>

  <link rel="icon" type="image/png" sizes="32x32" href="img/icons/favicon-32x32.png">
  <link rel="manifest" href="img/icons/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="img/icons/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/github-fork-ribbon-css/0.2.2/gh-fork-ribbon.min.css" />
</meta>
<body>
<a class="github-fork-ribbon" href="//github.com/patcon/civictechto-community-toolkit" data-ribbon="Contribute on GitHub" title="Contribute on GitHub">Contribute on GitHub</a>
<div id='main-container'>
  <div id='title-hero'>
  <h1 class='neuton'><?php echo getenv('TOOLKIT_TITLE'); ?></h1>
    <h3 class='neuton'>
      All the resources <span style='font-weight: 600'>you</span> can use to get oriented in our community.<br/>
      Have you created a helpful resource, or do you know of an existing one?
      <a href='https://docs.google.com/forms/d/e/1FAIpQLScPqlEWbCKt-jW8R4RF8SdQ33_iSmzLi7jFZ8wKC6KZussWFw/viewform' target='_blank'>
        Submit it here.
      </a>
    </h3>
  </div>
  <div id='filters'>
    <form id='toolkit-filters'>
      <ul>
        <li class='lato'><input type='radio' name='f' value='All' id='All' checked="checked"/>
          <label for='All'>All</label></li>
        <li class='lato'><input type='radio' name='f' value='Official' id='Official'/>
          <label for='Official'>Official</label></li>
        <li class='lato'><input type='radio' name='f' value='Organizing' id='Organizing'/>
          <label for='Organizing'>Organizing</label></li>
        <li class='lato'><input type='radio' name='f' value='Speaking' id='Speaking'/>
          <label for='Speaking'>Speaking</label></li>
        <li class='lato'><input type='radio' name='f' value='Pitching' id='Pitching'/>
          <label for='Pitching'>Pitching</label></li>
      </ul>
    </form>
  </div>
  <div id='canvas-area'>
    <p class='lato' id='loader'>Loading...</p>
  </div>
</div>
<footer class='lato'>
  <span>
    Made with ❤ for <a href='http://civitech.ca' style='display: inline-block'>Civic Tech Toronto</a>.
    <a href="https://github.com/patcon/civictechto-community-toolkit">Source code.</a>
    <a href="https://docs.google.com/spreadsheets/d/1i5HAu4uIiF9sRUJlKZEKISQlYSzlCXRTy1VlfShbraQ/edit#gid=0">Database.</a>
  </span>
</footer>
<script type='text/javascript' src="js/d3.js" charset="utf-8"></script>
<script type='text/javascript' src='js/jquery.js'></script>
<script type='text/javascript' src='js/deparam.js'></script>
<script type='text/javascript'>
  window.VolunteerToolkit = (function ($) {

    var VolunteerToolkit = function (initialFilter) {
      //initialFilter :: The filter that was inputted. If empty ignore
      this.DATA_URL = './data.php';
      this._currentFilter = initialFilter == undefined ? "All" : initialFilter;

      this.columnSize = 4; // Default
      this._initialized = false;

      this._pigeonhole = function (item) {
        var that = this;
        for (var x in that.columns) {
          var height = that.columns[x].reduce(function (sum, item) {
            return sum + $(item).height()
          }, 0);
        }
      };

      this.render = function (filter) {
        var that = this;
        if (!filter || filter == undefined) {
          filter = that._currentFilter;
        }
        else {
          that._currentFilter = filter;
        }

        //Setting columnSizes...
        var $canvasArea = $("#canvas-area");
        if ($canvasArea.width() > 900) {
          that.columnSize = 4;
        } else if ($canvasArea.width() > 600) {
          that.columnSize = 3;
        } else {
          that.columnSize = 2;
        }
        $canvasArea.attr("data-colcount", that.columnSize);

        var dataToShow = that.data;
        if (filter != 'All') {
          dataToShow = dataToShow.filter(function (d) {
            switch (filter) {
              case 'Official':
                return d.official;
              case 'Organizing':
                return d.organizing;
              case 'Speaking':
                return d.speaking;
              case 'Pitching':
                return d.pitching;
            }
            ;
          });
        }

        // Append all items if necessary
        var items = d3.select("#canvas-area").selectAll("div.item")
          .data(dataToShow, function (d) {
            return d.url;
          });
        /* set url as ID */

        items.enter()
          .append("div")
          .classed("item", true)
          .classed("is-new", function (d) {
            return d.isNew;
          })
          .html(function (d, i) {
            var html = "<div class='site-image' style='background-image: url(" + d.image + ")'><a class='lato' target='_blank' href='" + d.url + "'></a></div>"
              + "<div class='content'>"
              + "<h2 class='neuton'><a target='_blank' href='" + d.url + "'>" + d.title + "</a></h2>"
              + "<p class='lato'>" + d.description + "</p>"
              + "<a class='lato' href='" + d.url + "' target='_blank'>Go to site</a>";

            if (d.isNew) {
              html = "<div class='is-new-tag'>New!</div>" + html;
            }
            ;
            return html;
          });

        items.exit()
          .each(function (d) {
            d3.select(this).transition().style("opacity", 0)
              .each("end", function () {
                d3.select(this).remove();
              });
          });


        var columns = [];

        items
          .each(function (d, ind) {
            //Find proper column to put.
            var target = 0;
            var bottom = -1;
            for (var i = 0; i < that.columnSize; i++) {
              if (columns[i] == undefined || !columns) {
                target = i;
                bottom = 0;
                break;
              }

              if (bottom == -1 || bottom > columns[i]) {
                target = i;
                bottom = columns[i];
              }
            }

            // assume that the column by this time has been chosen
            var left = (target * (100 / that.columnSize));
            // $(this).css({ top: (bottom+20)+"px", left: left+"px" });
            // if (that._initialized) {
            d3.select(this)
              .transition()
              .duration(500)
              .style("opacity", 1);
            //   .style("opacity", 1)
            //   .style("top", (bottom)+"px")
            //   .style("left", left+"px");
            // } else {
            d3.select(this)
              .style("top", (bottom) + "px")
              .style("left", left + "%");

            columns[target] = $(this).position().top + $(this).height() + 20;
          }); // end of items.each()..

        d3.select("#canvas-area").style("height", d3.max(columns) + "px");

        that._initialized = true;


      };

      this.initialize = function () {
        var that = this;
        d3.csv(that.DATA_URL,
          function (d) {
            return {
              // parse items in obj
              url: d.url,
              title: d.title,
              description: d.description,
              image: d.image,
              official: d.official == "1",
              organizing: d.organizing == "1",
              speaking: d.speaking == "1",
              pitching: d.pitching == "1",
              isNew: d.isNew == "1"
            };
          },
          function (err, data) {
            that.data = data;

            //Push new items to the top
            var newItems = [];
            for (var i = that.data.length - 1; i > 0; i--) {
              if (that.data[i].isNew) {
                newItems.push(that.data.splice(i, 1)[0]);
              }
            }
            that.data = newItems.concat(that.data);


            that.render(this._currentFilter);

            d3.select("#loader").remove();
          });
      };

      this.initialize();
    };

    return {loaded: true, toolkit: VolunteerToolkit};
    //Load data

    //render data

    //React to filters
  })(jQuery);

  window.Manager = {};
  (function ($, window) {
    //listen to hashchange
    $("#toolkit-filters").on("submit", function () {
      window.location.hash = $(this).serialize();
      return false;
    });

    $("#toolkit-filters input[name=f]").on("change", function (event) {
      $("#toolkit-filters").submit();
    });

    $(window).on('hashchange', function () {
      var params = $.deparam(window.location.hash.substring(1));

      if (params.f) {
        $("input[name=f][id=" + params.f + "]").attr("checked", "checked");
      }
      if (!window.Manager.toolkit) {
        window.Manager.toolkit = new window.VolunteerToolkit.toolkit(params.f);
      } else {
        window.Manager.toolkit.render(params.f);
      }
    });
    $(window).trigger("hashchange");

    var rtime;
    var timeout = false;
    var delta = 200;

    $(window).on('resize', function () {
      rtime = new Date();
      if (timeout === false) {
        timeout = true;
        setTimeout(resizeEnd, delta);
      }
    });

    function resizeEnd() {
      if (new Date() - rtime < delta) {
        setTimeout(resizeEnd, delta);
      } else {
        timeout = false;
        window.Manager.toolkit.render();
      }
    }

  })(jQuery, window);
</script>
</body>
</html>
