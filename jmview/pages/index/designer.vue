<template>
	<view class="content">
		<vrow v-for="item in content">
		    <vcol span="35" class="content">
				   <image class="img" :src="item.avatar"></image>
		    </vcol>	
		    <vcol class="font28 lh5" span="65">
				<vrow class="mt10">
                   <vcol span="40" class="font32 fw">{{item.member_name}}</vcol>
				   <vcol span="25"><text class="red mr5 fw designerlevel">{{item.level_name}}</text></vcol>
				</vrow>
				<vrow class="mt10">擅长：{{item.advantage}}</vrow>	
				<vrow class="mt10"></vrow>	
		    </vcol>	
			<vcol class="font28 lh5" span="100">
                <navigator v-bind:url="'designerdetail?designerid='+item.member_id" open-type="navigate">
				  <button type="primary" class="designerlevel">关于设计师</button>
				</navigator>
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
	.designerlevel{
		padding: 10upx 15upx;
		background:-webkit-linear-gradient(left,#695648,#82858A); /* Safari 5.1 - 6.0 */
		background: rgb(132,133,135);/*chrom*//* linear-gradient(left,#FFCA00,#FF6633) */
		background:-ms-linear-gradient(left,#695648,#82858A);/* IE 10 */
		background:-moz-linear-gradient(left,#695648,#82858A);/*火狐*/ 
		background:-o-linear-gradient(left,#695648,#82858A); /* Opera 11.1 - 12.0 */
		color:#fff;
		border-radius: 10upx;
	}



</style>
