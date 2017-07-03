/*
 */
// <div class="FR"><a data-isliked="" data-id="" data-proc="likeswitch" href="#">LIKE</a></div>
/*
 Event.add(id('place'), "click", function(e){
 var processor = e.target.getAttribute('data-proc');
 //console.log(e.target, processor, window[processor]);
 if (window[processor]) window[processor](e.target);
 return false;
 });
 var d = {};
 d.id = i.id;
 d.avatar = {'src':i.profile_picture};
 d.author = {};
 d.author.username = ('@' + i.username).parseUsername().emoji(); // i.user.full_name
 d.author.userabout = i.full_name.emoji();
 d.author.bio = i.bio.emoji();
 d.author.website = i.website.emoji();
 d.counts = {};
 d.counts.followed_by = i.counts.followed_by;
 d.counts.follows = i.counts.follows;
 d.counts.media = i.counts.media;

 var userHeadFrag = createFrag(userHeadFragHTML);
 //console.log(userHeadFrag);
 renderFrag(userHeadFrag, d);
 var el = id('head');
 el.appendChild(userHeadFrag);

 Event.add(id('switchfollowuser'), "click", function(e){
 var processor = e.target.getAttribute('data-proc');
 console.log(e.target, processor);
 if (window[processor]) window[processor](e.target);
 return false;
 });
 window.likeswitch = function(e){
 var fid = e.getAttribute('data-id');
 var isliked = e.getAttribute('data-isliked');
 console.log('LIKE', fid, isliked);
 if (isliked == 'yes')
 {
 console.log('YES, LIKED. DISLIKE!');
 e.setAttribute('data-isliked','no');
 ajax('http://'+ajaxhost+':'+ajaxport+'/like', onLikeConfirmed, {}, 'POST', {'token': token, 'fid': fid, 'like': 'no'});
 }
 else {
 console.log('NO, NOT LIKED. I LIKE!');
 e.setAttribute('data-isliked','yes');
 ajax('http://'+ajaxhost+':'+ajaxport+'/like', onLikeConfirmed, {}, 'POST', {'token': token, 'fid': fid, 'like': 'yes'});
 }
 }
 window.swfollow = function(e){
 var fid = e.getAttribute('data-id');
 var isliked = e.getAttribute('data-follow');
 console.log('FOLLOW', fid, isliked);
 if (isliked == 'yes')
 {
 e.setAttribute('data-follow','no');
 ajax('http://'+ajaxhost+':'+ajaxport+'/follow', onLikeConfirmed, {}, 'POST', {'token': token, 'uid': fid, 'follow': 'no'});
 }
 else {
 e.setAttribute('data-follow','yes');
 ajax('http://'+ajaxhost+':'+ajaxport+'/follow', onLikeConfirmed, {}, 'POST', {'token': token, 'uid': fid, 'follow': 'yes'});
 }
 }
 */