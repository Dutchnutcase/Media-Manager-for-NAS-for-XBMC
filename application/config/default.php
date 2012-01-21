<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Video file extensions
// Do NOT change
$video_extensions[] = 'm4v';
$video_extensions[] = '3gp';
$video_extensions[] = 'nsv';
$video_extensions[] = 'ts';
$video_extensions[] = 'ty';
$video_extensions[] = 'strm';
$video_extensions[] = 'rm';
$video_extensions[] = 'rmvb';
$video_extensions[] = 'm3u';
$video_extensions[] = 'ifo';
$video_extensions[] = 'mov';
$video_extensions[] = 'qt';
$video_extensions[] = 'divx';
$video_extensions[] = 'xvid';
$video_extensions[] = 'bivx';
$video_extensions[] = 'vob';
$video_extensions[] = 'nrg';
$video_extensions[] = 'img';
$video_extensions[] = 'iso';
$video_extensions[] = 'pva';
$video_extensions[] = 'wmv';
$video_extensions[] = 'asf';
$video_extensions[] = 'asx';
$video_extensions[] = 'ogm';
$video_extensions[] = 'm2v';
$video_extensions[] = 'avi';
$video_extensions[] = 'bin';
$video_extensions[] = 'dat';
$video_extensions[] = 'dvr-ms';
$video_extensions[] = 'mpg';
$video_extensions[] = 'mpeg';
$video_extensions[] = 'mp4';
$video_extensions[] = 'mkv';
$video_extensions[] = 'avc';
$video_extensions[] = 'vp3';
$video_extensions[] = 'svq3';
$video_extensions[] = 'nuv';
$video_extensions[] = 'viv';
$video_extensions[] = 'dv';
$video_extensions[] = 'fli';
$video_extensions[] = 'flv';
$video_extensions[] = 'rar';
$video_extensions[] = '001';
$video_extensions[] = 'wpl';
$video_extensions[] = 'zip';

// Movie stacking
// Do NOT change
$movie_stacking[] = "(.*?)([ _.-]*(?:cd|dvd|p(?:ar)?t|dis[ck]|d)[ _.-]*[0-9]+)(.*?)(\.[^.]+)$";
$movie_stacking[] = "(.*?)([ _.-]*(?:cd|dvd|p(?:ar)?t|dis[ck]|d)[ _.-]*[a-d])(.*?)(\.[^.]+)$";
$movie_stacking[] = "(.*?)([ ._-]*[a-d])(.*?)(\.[^.]+)$";

// Clean date time
// Do NOT change
$clean_date_time = "(.+[^ _\,\.\(\)\[\]\-])[ _\.\(\)\[\]\-]+(19[0-9][0-9]|20[0-1][0-9])([ _\,\.\(\)\[\]\-][^0-9]|$)";

// Clean strings
// Do NOT change
$clean_strings[] = "[ _\,\.\(\)\[\]\-](ac3|dts|custom|dc|divx|divx5|dsr|dsrip|dutch|dvd|dvdrip|dvdscr|dvdscreener|screener|dvdivx|cam|fragment|fs|hdtv|hdrip|hdtvrip|internal|limited|multisubs|ntsc|ogg|ogm|pal|pdtv|proper|repack|rerip|retail|r3|r5|bd5|se|svcd|swedish|german|read.nfo|nfofix|unrated|ws|telesync|ts|telecine|tc|brrip|bdrip|480p|480i|576p|576i|720p|720i|1080p|1080i|hrhd|hrhdtv|hddvd|bluray|x264|h264|xvid|xvidvd|xxx|www.www|cd[1-9]|\[.*\])([ _\,\.\(\)\[\]\-]|$)";
$clean_strings[] = "(\[.*\])";

// Tvshow matching
// Do NOT change
$tvshow_matching[] = "\[[Ss]([0-9]+)\]_\[[Ee]([0-9]+)([^\\/]*)";
$tvshow_matching[] = "[\._ \-]([0-9]+)x([0-9]+)([^\\/]*)";
$tvshow_matching[] = "[\._ \-][Ss]([0-9]+)[\.\-]?[Ee]([0-9]+)([^\\/]*)";
$tvshow_matching[] = "[\._ \-]([0-9]+)([0-9][0-9])([\._ \-][^\\/]*)";
$tvshow_matching[] = "[\._ \-]p(?:ar)?t[._ -]()([ivxlcdm]+)([\._ \-][^\\/]*)";

// Tv multipart matching
// Do NOT change
$tv_multipart_matching = "^[-_EeXx]+([0-9]+)";

// Trailer matching
// Do NOT change
$trailer_matching[] = "(.*?)(_Trailer)(\.[^.]+)$";

// Exclude from scan
// Do NOT change
$exclude_from_scan[] = "-trailer";
$exclude_from_scan[] = "[-._ \\/]sample[-._ \\/]";

// Exclude tvshows from scan
// Do NOT change
$exclude_tvshows_from_scan[] = "[-._ \\/]sample[-._ \\/]";

// Fanart
// Do NOT change
$fanart[] = "fanart.jpg";
$fanart[] = "fanart.png";

/* End of file default.php */
/* Location: ./application/config/default.php */