<template>
	<view>
		<vrow>
		    <vcol span="40">
               <vrow>
				   <image class="img" src="../../static/images/design_dlrb.jpg"></image>
			   </vrow>
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

</style>
