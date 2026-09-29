modalLikeBtn.onclick = function(){
    fetch("like.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"album="+encodeURIComponent(currentAlbum)+"&photo="+encodeURIComponent(currentPhoto)
    })
    .then(function(r){return r.json();})
    .then(function(d){
        if(d && typeof d.likes !== "undefined"){
            modalLikeCount.innerText = d.likes;
        }
    });
};
