<?php
session_start();

$albumName = isset($_GET['name']) ? basename($_GET['name']) : '';
if ($albumName === '') {
    header('Location: my_gallery.php');
    exit;
}

$albumFsPath  = __DIR__ . "/uploads/albums/$albumName";
$albumWebPath = "uploads/albums/$albumName";

if (!is_dir($albumFsPath)) {
    http_response_code(404);
    die("<h2 style='text-align:center;margin-top:80px;color:#fff;background:#0b0f16;height:100vh;'>Album not found.</h2>");
}

$files = glob($albumFsPath . '/*.{jpg,jpeg,png,gif,webp,JPG,JPEG,PNG,GIF,WEBP}', GLOB_BRACE);
natsort($files);

$conn = new mysqli("localhost", "root", "", "portfolio_db");
if ($conn->connect_error) {
    die("DB connect failed");
}

function web_url($fsPath, $albumWebPath) {
    return $albumWebPath . '/' . rawurlencode(basename($fsPath));
}

$isAdmin = !empty($_SESSION['admin_logged_in']);

$commentStmt = $conn->prepare("SELECT COUNT(*) AS total FROM photo_comments WHERE album=? AND photo=?");
$hasCommentStmt = $commentStmt ? true : false;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars(ucfirst($albumName)); ?> Album</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body{font-family:system-ui,Arial;background:#0b0f16;margin:0;color:#fff;}
.wrap{max-width:1150px;margin:24px auto;padding:0 16px 40px;}
.topbar{display:flex;align-items:center;gap:12px;margin:12px 0 20px;font-size:14px;}
.topbar a{text-decoration:none;color:#ffb347;font-weight:600;}
h2{text-align:center;margin-bottom:20px;color:#ffb347;font-size:26px;}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px;}
.card{background:#111723;border-radius:16px;padding:10px 10px 12px;cursor:pointer;transition:.25s;border:1px solid rgba(255,255,255,.06);box-shadow:0 14px 32px rgba(0,0,0,.45);}
.card:hover{transform:translateY(-4px);}
.card img{width:100%;height:190px;object-fit:cover;border-radius:12px;display:block;}
.card-title{text-align:center;margin-top:8px;font-size:14px;opacity:.85;text-transform:capitalize;}
.thumb-meta{display:flex;justify-content:center;gap:8px;margin-top:6px;font-size:12px;}
.badge-comments{background:#e7f0ff;color:#111;border-radius:999px;padding:2px 9px;min-width:60px;text-align:center;}
.badge-comments span{font-weight:600;}

#photoModal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);justify-content:center;align-items:center;z-index:9999;overflow-y:auto;}
.modal-inner{background:#ffffff;border-radius:18px;max-width:950px;width:95%;margin:30px auto;color:#111;box-shadow:0 18px 50px rgba(0,0,0,.7);overflow:hidden;}
.modal-img-box{background:#000;padding:10px;display:flex;justify-content:center;}
.modal-img-box img{width:100%;max-height:80vh;object-fit:contain;border-radius:14px;}

.modal-bars{display:flex;}
.modal-like-bar,
.modal-comment-bar{
    flex:1;
    background:#ffe6ec;
    padding:8px 10px;
    font-size:13px;
    display:flex;
    justify-content:center;
    align-items:center;
    gap:6px;
    cursor:pointer;
}
.modal-comment-bar{background:#e7f4ff;}

.modal-download-bar{
    background:#ecffeb;
    padding:6px 10px;
    font-size:12px;
    text-align:center;
    border-top:1px solid #d0f2d0;
}

.modal-body{padding:14px 18px 18px;max-height:360px;overflow-y:auto;border-top:1px solid #eee;}
.comment-list-empty{opacity:.6;font-size:13px;}
.c-item{padding:10px 0;border-bottom:1px solid #eee;font-size:14px;}
.c-header{display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;gap:8px;}
.c-name{font-weight:600;}
.c-date{opacity:.65;}
.c-text{margin-bottom:6px;white-space:pre-wrap;}
.c-admin-reply{background:#f4f6ff;border-radius:8px;padding:6px 8px;font-size:13px;margin-top:4px;}
.c-actions{margin-top:6px;display:flex;gap:6px;}
.c-actions button{border:none;border-radius:6px;padding:5px 9px;font-size:11px;cursor:pointer;}
.c-reply{background:#e7f0ff;}
.c-delete{background:#ffe0e0;}

.comment-form{border-top:1px solid #eee;margin-top:10px;padding-top:10px;font-size:14px;}
.comment-form input,
.comment-form textarea{
    width:100%;border-radius:8px;border:1px solid #ccc;padding:8px 10px;margin-bottom:8px;
}
.comment-form textarea{min-height:70px;resize:vertical;}
.comment-form button{background:#111b2a;color:#fff;border:none;border-radius:8px;padding:8px 14px;cursor:pointer;font-weight:600;font-size:14px;width:100%;}

.modal-footer{background:#111b2a;padding:10px;text-align:center;}
.modal-close-btn{background:#000;color:#fff;border:none;border-radius:10px;padding:10px 24px;width:100%;max-width:260px;font-weight:600;cursor:pointer;}
</style>
</head>

<body>
<div class="wrap">
    <div class="topbar">
        <a href="my_gallery.php">← Back</a>
        <span>/</span>
        <span><?php echo htmlspecialchars(ucfirst($albumName)); ?></span>
    </div>

    <h2><?php echo htmlspecialchars(ucfirst($albumName)); ?> Album</h2>

    <div class="grid">
    <?php if (empty($files)): ?>
        <p style="grid-column:1/-1;text-align:center;opacity:.7;">No photos yet.</p>
    <?php else: ?>
        <?php foreach($files as $fsImg):
            $url = web_url($fsImg, $albumWebPath);
            $photoName = basename($fsImg);
            $commentsTotal = 0;
            if ($hasCommentStmt) {
                $commentStmt->bind_param("ss", $albumName, $photoName);
                if ($commentStmt->execute()) {
                    $cr = $commentStmt->get_result()->fetch_assoc();
                    $commentsTotal = isset($cr['total']) ? (int)$cr['total'] : 0;
                }
            }
        ?>
        <div class="card" data-photo="<?php echo htmlspecialchars($photoName); ?>" data-url="<?php echo htmlspecialchars($url); ?>" data-comments="<?php echo $commentsTotal; ?>">
            <img src="<?php echo htmlspecialchars($url); ?>" alt="">
            <div class="card-title"><?php echo htmlspecialchars(pathinfo($photoName, PATHINFO_FILENAME)); ?></div>
            <div class="thumb-meta">
                <span class="badge-comments">💬 <span class="thumb-comment-count"><?php echo $commentsTotal; ?></span></span>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>
</div>

<?php if ($commentStmt) $commentStmt->close(); ?>

<div id="photoModal">
    <div class="modal-inner">
        <div class="modal-img-box"><img id="modalImg"></div>

        <div class="modal-bars">
            <button id="modalLikeBtn" class="modal-like-bar">❤️ Like (<span id="modalLikeCount">0</span>)</button>
            <button id="modalCommentBtn" class="modal-comment-bar">💬 Comment</button>
        </div>

        <div class="modal-download-bar">
            <a id="modalDownload" href="#" download style="text-decoration:none;color:#0b5b18;font-weight:600;">⬇ Download</a>
        </div>

        <div class="modal-body">
            <div id="modalCommentList" class="comment-list-empty">No comments yet.</div>

            <div class="comment-form">
                <input type="text" id="commentName" placeholder="Your name (optional)">
                <textarea id="commentText" placeholder="Write a comment..."></textarea>
                <button id="sendComment">Post Comment</button>
            </div>
        </div>

        <div class="modal-footer">
            <button id="closeBtn" class="modal-close-btn">Close</button>
        </div>
    </div>
</div>

<script>
var currentAlbum = <?php echo json_encode($albumName); ?>;
var currentPhoto = "";
var currentCard = null;
var isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;

function escapeHtml(str){return str.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#039;");}

var modal = document.getElementById("photoModal");
var modalImg = document.getElementById("modalImg");
var modalDownload = document.getElementById("modalDownload");
var modalLikeCount = document.getElementById("modalLikeCount");
var modalCommentList = document.getElementById("modalCommentList");
var commentNameInput = document.getElementById("commentName");
var commentTextInput = document.getElementById("commentText");

document.querySelectorAll(".card").forEach(function(card){
    card.addEventListener("click",function(){
        currentCard = card;
        currentPhoto = card.getAttribute("data-photo");
        var url = card.getAttribute("data-url");
        modalImg.src = url;
        modalDownload.href = url;
        loadLikes();
        loadComments();
        modal.style.display = "flex";
    });
});

document.getElementById("closeBtn").onclick = function(){
    modal.style.display = "none";
};
modal.onclick = function(e){if(e.target.id==="photoModal"){modal.style.display="none";}};
document.addEventListener("keydown",function(e){if(e.key==="Escape"){modal.style.display="none";}});

function loadLikes(){
    if(!currentPhoto) return;
    fetch("load_likes.php?album="+encodeURIComponent(currentAlbum)+"&photo="+encodeURIComponent(currentPhoto))
    .then(r=>r.json()).then(d=>{
        modalLikeCount.innerText = d.likes ?? 0;
    });
}

document.getElementById("modalLikeBtn").onclick = function(){
    if(!currentPhoto) return;
    fetch("like.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"album="+encodeURIComponent(currentAlbum)+"&photo="+encodeURIComponent(currentPhoto)
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.status==="success"){modalLikeCount.innerText=d.likes;}
    });
};

function renderComments(list){
    modalCommentList.innerHTML = "";
    if(!list || list.length===0){
        modalCommentList.classList.add("comment-list-empty");
        modalCommentList.innerHTML = "No comments yet.";
    } else {
        modalCommentList.classList.remove("comment-list-empty");
        list.forEach(function(c){
            var html = "";
            html += '<div class="c-item" data-id="'+c.id+'">';
            html += '<div class="c-header">';
            html += '<span class="c-name">'+escapeHtml(c.user_name || "Guest")+'</span>';
            html += '<span class="c-date">'+escapeHtml(c.created_at)+'</span>';
            html += '</div>';
            html += '<div class="c-text">'+escapeHtml(c.comment)+'</div>';
            if(c.admin_reply){
                html += '<div class="c-admin-reply"><strong>Admin:</strong> '+escapeHtml(c.admin_reply)+'</div>';
            }
            if(isAdmin){
                html += '<div class="c-actions">';
                html += '<button class="c-reply" data-id="'+c.id+'">Reply</button>';
                html += '<button class="c-delete" data-id="'+c.id+'">Delete</button>';
                html += '</div>';
            }
            html += '</div>';
            modalCommentList.innerHTML += html;
        });
    }
    if(currentCard){
        let badge = currentCard.querySelector(".thumb-comment-count");
        if(badge) badge.textContent = list.length;
    }
}

function loadComments(){
    fetch("load_comments.php?album="+encodeURIComponent(currentAlbum)+"&photo="+encodeURIComponent(currentPhoto))
    .then(r=>r.json())
    .then(list=>{renderComments(list);});
}

document.getElementById("sendComment").onclick = function(){
    var txt = commentTextInput.value.trim();
    var nm = commentNameInput.value.trim();
    if(txt === "") return;

    fetch("save_comment.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:
            "album="+encodeURIComponent(currentAlbum)+
            "&photo="+encodeURIComponent(currentPhoto)+
            "&comment="+encodeURIComponent(txt)+
            "&name="+encodeURIComponent(nm)
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.status==="success"){
            commentTextInput.value="";
            commentNameInput.value="";
            loadComments();
        }
    });
};

modalCommentList.addEventListener("click",function(e){
    if(!isAdmin) return;
    var id = e.target.getAttribute("data-id");
    if(!id) return;

    if(e.target.classList.contains("c-reply")){
        var reply = prompt("Write admin reply:");
        if(!reply) return;
        fetch("reply_comment.php",{
            method:"POST",
            headers:{"Content-Type":"application/x-www-form-urlencoded"},
            body:"id="+encodeURIComponent(id)+"&reply="+encodeURIComponent(reply)
        })
        .then(r=>r.json())
        .then(d=>{
            if(d.status==="success"){loadComments();}
        });
    }
    else if(e.target.classList.contains("c-delete")){
        if(!confirm("Delete this comment?")) return;
        fetch("delete_comment.php",{
            method:"POST",
            headers:{"Content-Type":"application/x-www-form-urlencoded"},
            body:"id="+encodeURIComponent(id)
        })
        .then(r=>r.json())
        .then(d=>{
            if(d.status==="success"){loadComments();}
        });
    }
});

document.getElementById("modalCommentBtn").onclick = function(){
    commentTextInput.focus();
};
</script>

</body>
</html>
