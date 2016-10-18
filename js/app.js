ouyaApp=angular.module('OuYaApp', ['ngMaterial', 'angular-clipboard','pascalprecht.translate','chart.js']);
ouyaApp.config(['$translateProvider',  function ($translateProvider) {
    // add translation table
    $translateProvider.useStaticFilesLoader({
        prefix: 'js/',
        suffix: '.json'
    });
    $translateProvider.preferredLanguage('en');
    $translateProvider.useSanitizeValueStrategy('escapeParameters');

}]);

ouyaApp.controller('NotFoundController', ['$scope', '$http', '$translate', function($scope,$http,$translate) {


    $scope.myHost = location.hostname;

    if (location.hostname.indexOf('hurz')>=0 || location.hostname.indexOf('rx8')>=0) {
        $translate.use('de');
    }
}]);

ouyaApp.controller('StatsController', ['$scope', '$http', '$translate',  function($scope,$http,$translate) {
    $scope.myHost = location.hostname;
    $scope.noData=true;
    $scope.chartOptions= {
        scales: {
            yAxes: [{
                display: true,
                ticks: {
                    beginAtZero: true   // minimum value will be 0.
                }
            }]
    }};

    $scope.CollectStats=function (token) {
        $http.get('./php/stats.php?token='+token)
            .then(
                function(response){
                    $scope.data=[];
                    $scope.labels=[];
                    response.data.forEach(function(entry) {
                        $scope.noData=false;
                        $scope.labels.push(entry.year+"-"+entry.month+"-"+entry.day);
                        $scope.data.push(entry.count);
                    })
                }
            );
    };

    $scope.Init=function () {
        $scope.token=gup('token');
        if ($scope.token===null) {
            $scope.token=pathBefore("stats");
        }
        if ($scope.token!==undefined) {
            $scope.CollectStats($scope.token);
        }
    };

    if (location.hostname.indexOf('hurz')>=0 || location.hostname.indexOf('rx8')>=0) {
        $translate.use('de');
    }



}]);

ouyaApp.controller('MainController', ['$scope', '$http', '$translate', function($scope,$http,$translate) {
    $scope.myHost = location.hostname;


    $scope.customUrl= {
        enabled:false
    };
    $scope.expiration={
        enabled:false,
        minutes:0,
        hours:0,
        days:7
    };

    $scope.checks={
        first:false,
        second:false
    };


    $scope.expirationText=[];
    $scope.customUrlText=[];
    $scope.target={value:""};

    $scope.responseUrl= {
        Short:'dbddhkp',

        Full:function () {
            return "http://"+$scope.myHost+"/"+this.Short;
        }
    };
    $scope.responseUrl.Short=undefined;

    $scope.LoadTranslations=function () {

        $translate('ExpirationSwitchOff').then(function (value) {
            $scope.expirationText[false] = value;
        });
        $translate('ExpirationSwitchOn').then(function (value) {
            $scope.expirationText[true] = value;
        });
        $translate('CustomUrlSwitchOff').then(function (value) {
            $scope.customUrlText[false] = value;
        });
        $translate('CustomUrlSwitchOn').then(function (value) {
            $scope.customUrlText[true] = value;
        });
        $translate('ExpirationText').then(function (value) {
            $scope.expirationTextPreview = value;
        });
        $translate('ErrorShortInUse').then(function (value) {
            $scope.ErrorIsInUseText = value;
        });
    };

    $scope.SwitchCustom=function () {
        if ($scope.customUrl.enabled) {
            $scope.expiration.enabled=true;
        }
    };

    $scope.Copied=function () {
        $scope.copySuccess=true ;
    };

    $scope.HumanReadableExpiration=function() {

        if (!$scope.expirationTextPreview) return;
        return $scope.expiration.enabled
            ?$scope.expirationTextPreview.replace("_DAYS_",$scope.expiration.days).replace("_HOURS_",$scope.expiration.hours).replace("_MINUTES_",$scope.expiration.minutes)
            :"";
    };

    $scope.CheckToken=function(token) {
        if (token===undefined) return;

        $scope.customCount=undefined;
        $scope.shortUrlPhpError=undefined;

        $http.post('./php/checktoken.php', {token:token} )
            .then(
                function(response){
                    if (!response.data.success) {
                        $scope.shortUrlPhpError=response.data.error;
                        $scope.shortForm.$invalid=true;
                    } else {
                        $scope.customCount = response.data.value;
                        $scope.customUrl.$invalid=response.data.value!=0;
                    }
                },
                function(response){
                    $scope.shortUrlPhpError=response;
                }
            );
    };

    $scope.AddShortUrl=function () {
        $scope.customCount=undefined;
        $scope.shortUrlPhpError=undefined;
        $scope.checks.first=true;

        $http.post('./php/addurl.php',
            {
                token:$scope.customUrl,
                checks:$scope.checks,
                target:$scope.target,
                expiration: $scope.expiration,
                debug:false
            } )
            .then(
                function(response){
                    $scope.saveSuccess=response.data.success;
                    if (!response.data.success) {
                        if (response.data.isinuse) {
                            $scope.saveError=$scope.ErrorIsInUseText;
                        } else {
                            $scope.saveError = response.data.error;
                        }
                    } else {
                        $scope.responseUrl.Short=response.data.url;
                    }
                },
                function(response){
                    $scope.saveError=response.data.error;
                }
            );
    };

    if (location.hostname.indexOf('hurz')>=0 || location.hostname.indexOf('rx8')>=0) {
        $translate.use('de').then(function () {
            $scope.LoadTranslations();
        });
    } else {
        $scope.LoadTranslations();
    }
}]);

function gup( name, url ) {
    if (!url) url = location.href;
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec( url );
    return results == null ? null : results[1];
}

function pathBefore(before, url) {
    if (!url) url = location.href;
    if (url.indexOf('/')<0) {
        return null;
    }
    var path = url.split('/');
    if (path.indexOf(before)<1) {
        return null;
    }
    return path[path.indexOf(before)-1];

}