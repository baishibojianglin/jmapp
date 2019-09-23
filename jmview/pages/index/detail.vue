<template>
	<view>
		<vrow>
		    <vcol span="30">
               <vrow class="content">
				   <image class="img" :src="article_content.headimg"></image>
			   </vrow>
		    </vcol>	
		    <vcol span="60">
		    </vcol>	
		</vrow>
	</view>
</template>

<script>
	import vrow from '@/components/lml-layout/row.vue'
	import vcol from '@/components/lml-layout/col.vue'
	
	export default {
		components:{
			vrow,vcol
		},
		data() {
			return {
		      article_content:{}
			}
		},
		onLoad(options) {
	        //加载文章详情内容
	         var self=this;
	         uni.request({
	        	url:'/dpc/index/Index/detail',
				data:{id:options.titleid},
	        	method:'POST',
				header:{'content-type':'application/x-www-form-urlencoded'},
	        	success:function(res){
	        		self.article_content=res.data;
	        	}
	         }) 
		},
		methods: {

		}
	}
</script>

<style>
	.content {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}
	.img {
		width:150upx;
		height:150upx;
		border-radius: 100%;
		margin: 15upx;
	}
</style>
