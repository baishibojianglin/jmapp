<template>
	<view class="content">
		<vrow v-for="item in content">
		    <vcol span="35" class="content">
				   <image class="img" :src="item.avatar"></image>
		    </vcol>	
		    <vcol class="font28 lh5" span="65">
				<vrow>
                   <vcol span="50">姓名：{{item.member_name}}</vcol>
				   <vcol span="15"><text class="red mr5 fw">{{item.level_id}}</text> 星级</vcol>
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
		      content:[]
			}
		},
		onLoad(options) {
	        //加载文章详情内容
	         var self=this;
	         uni.request({
	        	url:this.$url+'index.php/index/Index/designer',
	        	method:'GET',
	        	success:function(res){
					let data_long=res.data.length;
					for(let i=0;i<data_long;i++){
						res.data[i].avatar=self.$url+res.data[i].avatar.replace(/\\/g, "/");
					}				
	        		self.content=res.data;
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
		width:200upx;
		height:200upx;
		border-radius: 100%;
		margin: 15upx;
	}
	.designright{
		box-sizing:border-box;
		padding: 10upx 5upx;
	}



</style>
