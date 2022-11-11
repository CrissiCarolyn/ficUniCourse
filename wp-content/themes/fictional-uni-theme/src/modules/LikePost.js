import axios from "axios"

class LikePost {
  constructor() {
    if (document.querySelector(".post-like-box")) {
      axios.defaults.headers.common["X-WP-Nonce"] = universityData.nonce
      this.events()
    }
  }

  events() {
    document.querySelector(".post-like-box").addEventListener("click", e => this.ourPostClickDispatcher(e))
  }

  // methods
  ourPostClickDispatcher(e) {
    let postCurrentLikeBox = e.target
    while (!postCurrentLikeBox.classList.contains("post-like-box")) {
      postCurrentLikeBox = postCurrentLikeBox.parentElement
    }

    if (postCurrentLikeBox.getAttribute("data-exists-post") == "yes") {
      this.deleteLikePost(postCurrentLikeBox)
    } else {
      this.createLikePost(postCurrentLikeBox)
    }
  }

  async createLikePost(postCurrentLikeBox) {
    try {
      const response = await axios.post(universityData.root_url + "/wp-json/university/v1/managePostLike", {"blogId": postCurrentLikeBox.getAttribute("data-blog") })
      if (response.data != "Only logged in users can create a like.") {
        postCurrentLikeBox.setAttribute("data-exists-post", "yes")
        var postLikeCount = parseInt(postCurrentLikeBox.querySelector(".post-like-count").innerHTML, 10)
        postLikeCount++
        postCurrentLikeBox.querySelector(".post-like-count").innerHTML = postLikeCount
        postCurrentLikeBox.setAttribute("post-data-like", response.data)
      }
      console.log(response.data)
    } catch (e) {
      console.log("Sorry")
    }
  }

  async deleteLikePost(postCurrentLikeBox) {
    try {
      const response = await axios({
        method: 'delete',
        url: universityData.root_url + "/wp-json/university/v1/managePostLike", 
        data: { "like": postCurrentLikeBox.getAttribute("post-data-like") },
        timeout: 2
      })
      postCurrentLikeBox.setAttribute("data-exists-post", "no")
      var postLikeCount = parseInt(postCurrentLikeBox.querySelector(".post-like-count").innerHTML, 10)
      postLikeCount--
      postCurrentLikeBox.querySelector(".post-like-count").innerHTML = postLikeCount
      postCurrentLikeBox.setAttribute("post-data-like", "")
      console.log(response.data)
    } catch (e) {
      console.log(e)
    }
  }
}

export default LikePost
