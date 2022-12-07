<script>
import { mapState } from 'vuex'
import { Mixins } from 'vimeography-blueprint'

const defaultTemplate = `
  <figure class="vimeography-thumbnail">
    <router-link class="vimeography-link" :to="this.query" :title="video.name" exact exact-active-class="vimeography-link-active">
      <div class="vimeography-thumbnail-overlay">
        <svg aria-hidden="true" data-prefix="far" data-icon="play-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-play-circle fa-w-16 fa-9x"><path fill="white" d="M371.7 238l-176-107c-15.8-8.8-35.7 2.5-35.7 21v208c0 18.4 19.8 29.8 35.7 21l176-101c16.4-9.1 16.4-32.8 0-42zM504 256C504 119 393 8 256 8S8 119 8 256s111 248 248 248 248-111 248-248zm-448 0c0-110.5 89.5-200 200-200s200 89.5 200 200-89.5 200-200 200S56 366.5 56 256z" class=""></path></svg>
      </div>
      <img class="vimeography-thumbnail-img" v-lazy="thumbnailUrl" :alt="video.name" />
    </router-link>
    <figcaption>
      <h2 class="vimeography-title">{{video.name}}</h2>
    </figcaption>
  </figure>
`;

const userTemplate = document.querySelector('#vimeography-shadow-thumbnail');

const Thumbnail = {
  props: ['video'],
  mixins: [Mixins.Thumbnail],
  template: userTemplate ? userTemplate.innerText : defaultTemplate,
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
      galleryId: state => state.id
    })
  }
}

export default Thumbnail;
</script>

<style lang="scss" scoped>
  @import url('https://fonts.googleapis.com/css?family=Montserrat:400,600');

  .vimeography-thumbnail {
    margin: 0;
    max-width: 100%;
  }

  /* IE11 Support */
  @media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
    .vimeography-thumbnail {
      flex: 0 0 25%;
    }
  }

  .vimeography-link {
    display: block;
    position: relative;
    font-size: 0;
    line-height: 0;

    overflow: hidden;
    cursor: pointer;
    margin: 0 0 15px;
    box-shadow: inset 0 0 7px rgba(0, 0, 0, 0.2);
    height: 157px;
    transition: all 250ms;

    &:hover {
      box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.35);
    }
  }

  .vimeography-link-active {

  }

  .vimeography-thumbnail-overlay {
    display: flex;
    position: absolute;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;

    svg {
      transition: all 250ms;
      width: 40px;
      height: 40px;
    }

    &:hover {
      svg {
        width: 44px;
        height: 44px;
      }
    }
  }

  .vimeography-thumbnail-img {
    cursor: pointer;
    object-fit: cover;
    width: 100%;
    height: 100%;

    /* Required for inset shadow */
    position: relative;
    z-index: -1;
  }

  .vimeography-title {
    font-size: 14px;
    line-height: 1.3rem;

    text-align: left;
    font-family: "Montserrat", sans-serif;
    font-weight: 600;
    color: #2a282b;
    overflow: hidden;
    background: none;
  }
</style>
