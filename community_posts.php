<?php include "header.php"; ?> 

<?php
$conn = new mysqli("localhost", "root", "", "portfolio_db");
$posts = $conn->query("SELECT * FROM travel_posts WHERE status='approved' ORDER BY created_at DESC");
?>

<style>
.community-wrap{max-width:1100px;margin:40px auto;padding:0 20px;}
.community-title{text-align:center;font-size:32px;margin-bottom:25px;font-weight:bold;}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:22px;}
.card{background:white;padding:18px;border-radius:12px;box-shadow:0 3px 20px rgba(0,0,0,0.12);transition:0.2s;}
.card:hover{transform:scale(1.02);}
.card img{width:100%;height:200px;object-fit:cover;border-radius:10px;margin-bottom:12px;}
.card h3{margin:0 0 6px;font-size:20px;}
.card .name{font-size:14px;opacity:0.7;margin-bottom:8px;}
.card p{font-size:15px;line-height:1.4;}
.date{font-size:13px;text-align:right;margin-top:10px;opacity:0.6;}
.action-row{margin-top:12px;display:flex;justify-content:space-between;}
.like-btn,.comment-btn{padding:7px 12px;border:none;border-radius:8px;cursor:pointer;font-size:14px;font-weight:bold;color:#333;}
.like-btn{background:#ffe6ea;}
.comment-btn{background:#e7f0ff;}
#commentModal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);justify-content:center;align-items:center;z-index:9999;}
.modal-box{background:white;padding:18px;border-radius:10px;width:380px;max-height:80vh;overflow-y:auto;}
.modal-box h3{margin-top:0;margin-bottom:12px;}
#commentList p{border-bottom:1px solid #ddd;padding-bottom:6px;margin-bottom:6px;}
textarea{width:100%;height:70px;padding:8px;border-radius:8px;border:1px solid #bbb;margin-top:10px;}
#sendComment{margin-top:8px;background:#4caf50;padding:8px 12px;border:none;color:white;border-radius:6px;cursor:pointer;}
#closeComment{margin-top:8px;background:#333;padding:8px 12px;border:none;color:white;border-radius:6px;cursor:pointer;}
</style>

<div class="community-wrap">
    <h1 class="community-title">🌍 Community Travel Posts</h1>

    <div class="grid">
        <?php if(!$posts || $posts->num_rows==0): ?>
            <p style="grid-column:1 / -1; text-align:center; opacity:.7;">No posts available yet.</p>
        <?php else: ?>
            <?php while($p=$posts->fetch_assoc()): ?>
                <?php
                $pid = (int)$p['id'];
                $q  = $conn->query("SELECT COUNT(*) AS total FROM post_likes WHERE post_id=$pid");
                $likes = $q->fetch_assoc()['total'];
                ?>
                <div class="card">
                    <?php if(!empty($p['image'])): ?>
                        <img src="<?php echo htmlspecialchars($p['image']); ?>">
                    <?php endif; ?>

                    <h3><?php echo htmlspecialchars($p['title']); ?></h3>
                    <div class="name">✦ Posted by: <?php echo htmlspecialchars($p['name']); ?></div>
                    <p><?php echo nl2br(htmlspecialchars($p['story'])); ?></p>

                    <div class="action-row">
                        <button class="like-btn" data-id="<?php echo $pid; ?>">
                            ❤️ Like (<span><?php echo $likes; ?></span>)
                        </button>
                        <button class="comment-btn" data-id="<?php echo $pid; ?>">
                            💬 Comment
                        </button>
                    </div>

                    <div class="date">📅 <?php echo date("F d, Y", strtotime($p['created_at'])); ?></div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<div id="commentModal">
    <div class="modal-box">
        <h3>Comments</h3>
        <div id="commentList"></div>
        <textarea id="commentText" placeholder="Write a comment..."></textarea>
        <button id="sendComment">Post</button>
        <button id="closeComment">Close</button>
    </div>
</div>

<script>
let activePostID = 0;

document.querySelectorAll('.like-btn').forEach(btn=>{
    btn.addEventListener("click", function(){
        let id = this.getAttribute("data-id");
        let span = this.querySelector("span");

        fetch("like_post.php", {
            method:"POST",
            headers:{ "Content-Type":"application/x-www-form-urlencoded" },
            body:"post_id="+encodeURIComponent(id)
        })
        .then(r => r.json())
        .then(d => {
            if(d.status === "success"){
                span.innerText = d.likes;
            }
        });
    });
});

document.querySelectorAll('.comment-btn').forEach(btn=>{
    btn.addEventListener("click", function(){
        activePostID = this.getAttribute("data-id");
        loadComments();
        document.getElementById("commentModal").style.display = "flex";
    });
});

document.getElementById("closeComment").onclick = function(){
    document.getElementById("commentModal").style.display = "none";
};

function loadComments(){
    fetch("load_comments.php?post_id=" + activePostID)
    .then(r => r.json())
    .then(list => {
        let box = document.getElementById("commentList");
        box.innerHTML = "";
        if(list.length === 0){
            box.innerHTML = "<p style='opacity:.6;'>No comments yet.</p>";
            return;
        }
        list.forEach(c=>{
            box.innerHTML += `<p>${c.comment}<br><span style="font-size:12px;opacity:.6">${c.created_at}</span></p>`;
        });
    });
}

document.getElementById("sendComment").onclick = function(){
    let text = document.getElementById("commentText").value;
    if(text.trim() === "") return;

    fetch("save_comment.php", {
        method:"POST",
        headers:{ "Content-Type":"application/x-www-form-urlencoded" },
        body:"post_id="+activePostID+"&comment="+encodeURIComponent(text)
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.status === "success"){
            document.getElementById("commentText").value = "";
            loadComments();
        }
    });
};
</script>

<?php include "footer.php"; ?>
