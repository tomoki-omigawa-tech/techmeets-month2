import PostItem from './PostItem';

function PostList({ posts }) {
  if (posts.length === 0) {
    return <p>投稿がありません。</p>;
  }

  return (
    <div>
      {posts.map((post) => (
        <PostItem key={post.id} post={post} />
      ))}
    </div>
  );
}

export default PostList;