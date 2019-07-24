<script>
import { mapState } from 'vuex'
import { Mixins, DownloadLink } from 'vimeography-blueprint'

const defaultTemplate = `
  <figure class="vimeography-thumbnail">
    <router-link class="vimeography-link" :to="this.query" :title="video.name" exact exact-active-class="vimeography-link-active">

      <div class="vimeography-thumbnail-image-wrapper">
        <img class="vimeography-thumbnail-img" :src="thumbnailUrl" :alt="video.name" />
      </div>

      <figcaption class="vimeography-info">
        <h2 class="vimeography-title">{{video.name}}</h2>
        <div class="vimeography-description" v-html="video.description"></div>
      </figcaption>
    </router-link>
    <download-link :video="video"></download-link>
  </figure>
`;

const userTemplate = document.querySelector('#vimeography-playlister-thumbnail');

const Thumbnail = {
  props: ['video'],
  mixins: [Mixins.Thumbnail],
  template: userTemplate ? userTemplate.innerText : defaultTemplate,
  components: {
    DownloadLink
  },
  computed: {
    query() {
      const q = {
        ...this.$route.query,
        vimeography_gallery: this.$store.state.gallery.id,
        vimeography_video: this.video.id
      };

      return '?' + Object.keys(q).map(k => k + '=' + encodeURIComponent(q[k])).join('&')
    },
    ...mapState({
      galleryId: state => state.id,
    })
  }
}

export default Thumbnail;
</script>

<style lang="scss" scoped>
  .vimeography-thumbnail {
    width: 100%;
    margin: 0;
    border-bottom: 1px solid #333;
    outline: 0;
  }

  .vimeography-link {
    display: flex;
    align-items: flex-start;
    outline: 0;
    box-shadow: none;
    padding: 10px;
    background-color: #444;
    text-decoration: none;
    transition: all 0.25s ease-in-out;
  }

  .vimeography-title {
    transition: all 0.25s ease-in-out;
    font-size: 0.75rem;
    font-weight: 500;
    line-height: 1rem;
    margin: 0;
    padding: 0 0 5px;
    color: #ddd;
  }

  .vimeography-description {
    transition: all 0.25s ease-in-out;
    font-size: 0.7rem;
    line-height: 1.2em;
    margin: 0;
    padding: 0;
    color: #bbb;
  }

  .vimeography-link:hover,
  .vimeography-link-active {
    background-color: #555;
    outline: 0;
    box-shadow: none;

    .vimeography-title {
      color: #fff;
    }

    .vimeography-description {
      color: #ddd;
    }
  }

  .vimeography-thumbnail-image-wrapper {
    flex: 0 0 20%;
  }

  .vimeography-thumbnail-img {
    max-width: 100%;
    border-radius: 2px;
    align-self: center;
    box-shadow: none;
  }

  .vimeography-info {
    flex: 0 0 80%;
    padding: 0 10px;
    box-sizing: border-box;
  }

  /deep/ .vimeography-download {
    padding: 0 0 5px 10px;
  }

  @media all and (min-width: 940px) {
    .vimeography-thumbnail-image-wrapper {
      flex: 0 0 30%;
    }

    .vimeography-info {
      flex: 0 0 70%;
    }
  }
</style>
