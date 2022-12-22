<script>
  import { Player } from 'vimeography-blueprint';
  import { mapState } from 'vuex'
  import get from 'lodash/get';

  // Import Swiper and modules
  import {
    Swiper,
    Navigation,
    Pagination
  } from 'swiper/dist/js/swiper.esm.js';

  // Install modules
  Swiper.use([Navigation, Pagination]);

  require('../../node_modules/swiper/dist/css/swiper.min.css');

  const template = `
    <div class="vimeography-players-container" v-observe-visibility="visibilityChanged">
      <div class="swiper-container">
        <div class="swiper-wrapper">
          <div class="swiper-slide" v-for="(video, index) in videos">
            <player
              v-bind:activeVideo="video"
              v-bind:index="index"
              v-bind:key="video.id">
            </player>
            <h2 class="vimeography-title">{{video.name}}</h2>
            <div :v-html="video.description" class="vimeography-description"></div>
            <div class="vimeography-downloads" v-if="allowDownloads">
              <a :href="downloadLink(video)" :title="'Download ' + video.name">Download Video</a>
            </div>
          </div>
        </div>
      </div>

      <div class="swiper-button-prev" ref="prev"></div>
      <div class="swiper-button-next" ref="next"></div>
    </div>
  `;

  const PlayersContainer = {
    props: ['videos', 'activeVideoId'],
    template,
    components: {
      Player
    },
    methods: {
      reload: function () {
        setTimeout(function () {

          this.swiper.update();
          this.swiper.navigation.update();
          this.swiper.updateSize()
          this.swiper.updateSlides()
          this.swiper.updateProgress()
          this.swiper.updateSlidesClasses()

        }.bind(this), 250)
      },
      downloadLink: function(video) {
        if (video.download) {
          return video.download.filter( d => d.quality == "hd" )[0].link || null
        } else {
          return null
        }
      },
      visibilityChanged: function (isVisible) {
        if (isVisible) {
          this.reload();
        }
      }
    },
    computed: {
      ...mapState({
        allowDownloads: state => get(state, 'gallery.settings.downloads.enabled')
      })
    },
    updated: function() {
      this.reload();
    },
    watch: {
      activeVideoId(id) {
        let index = this.$store.getters.getVideoIndex(id);
        this.swiper.slideTo(index);
      }
    },
    mounted: function() {
      let initialSlide = this.$store.getters.getVideoIndex(this.activeVideoId) + 1;

      this.swiper = new Swiper(this.$el.childNodes[0], {
        initialSlide,
        spaceBetween: 10,
        setWrapperSize: true,
        navigation: {
          nextEl: this.$refs.next,
          prevEl: this.$refs.prev,
        },
        observer: true,
        observeParents: true
      });

    },
  }

  export default PlayersContainer;
</script>

<style lang="scss" scoped>
  .vimeography-players-container {
    position: relative;
  }

  /deep/ .vimeography-player-container {
    flex: 1;
  }

  .swiper-slide {
    flex-shrink: 0;
    height: 100%;
    width: auto;
    position: relative;
  }

  .swiper-button-prev,
  .swiper-button-next {
    cursor: pointer;
    width: 12px;
    height: 20px;
    margin-top: -10px;
    background-size: 12px 20px;
  }

  .swiper-button-prev {
    left: -20px;
  }

  .swiper-button-next {
    right: -20px;
  }
</style>
