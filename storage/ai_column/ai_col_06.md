# AI 工作流编排：多 Agent 协作与状态管理

单个 AI Agent 能处理的任务有限。当面对复杂需求时——比如"调研竞品、写分析报告、生成 PPT"——需要多个 Agent 分工协作。**工作流编排（Workflow Orchestration）**就是管理这种协作的框架。

---

## 为什么需要工作流编排

假设你要做一个"智能客服系统"，如果只有一个 Agent：

- 它既要理解用户问题，又要查订单，又要处理退款，还要安抚情绪
- 上下文越来越长，容易"失忆"
- 错误难以定位，一个环节出问题全盘崩溃

**拆分后的多 Agent 架构：**

```
用户消息 → 意图识别 Agent → 路由分发
                              ├─→ 订单查询 Agent
                              ├─→ 退款处理 Agent
                              ├─→ 技术支持 Agent
                              └─→ 情绪安抚 Agent（兜底）
                                          ↓
                              结果汇总 → 回复生成 Agent
```

每个 Agent 只负责一件事，专业且稳定。

---

## 常见编排模式

### 1. 串行管道（Pipeline）

任务按固定顺序执行，前一步的输出作为后一步的输入：

```
原始数据 → 清洗 Agent → 分析 Agent → 报告 Agent → 输出
```

适用场景：数据处理、内容生成流水线。

### 2. 并行分支（Parallel）

多个 Agent 同时处理不同维度，最后汇总：

```
              ├─→ 情感分析 Agent
用户评论 → ─┼─→ 关键词提取 Agent
              └─→ 意图识别 Agent
                          ↓
                    汇总 Agent
```

适用场景：舆情分析、多维评估。

### 3. 条件路由（Router）

根据中间结果动态决定下一步：

```
用户问题 → 分类 Agent ──技术问题──→ 技术支持 Agent
                      ──订单问题──→ 订单 Agent
                      ──投诉──────→ 升级处理 Agent
```

适用场景：客服分流、智能路由。

### 4. 循环迭代（Loop）

Agent 反复执行直到满足终止条件：

```
代码生成 Agent → 测试 Agent ──通过？──→ 结束
                    └─不通过──→ 修复 Agent → 循环
```

适用场景：代码生成、自动调优。

---

## 状态管理：工作流的"内存"

多 Agent 协作需要共享状态。常用方案：

### 1. 全局状态对象

```typescript
interface WorkflowState {
  input: string;
  context: Record<string, any>;
  results: AgentResult[];
  currentStep: number;
  isComplete: boolean;
}

const state: WorkflowState = {
  input: '用户原始问题',
  context: {},
  results: [],
  currentStep: 0,
  isComplete: false,
};
```

### 2. 消息总线（Event Bus）

Agent 不直接调用彼此，而是通过事件通信：

```typescript
eventBus.on('order.found', (data) => {
  refundAgent.handle(data);
});

eventBus.on('refund.approved', (data) => {
  notificationAgent.send(data);
});
```

解耦、可扩展、支持异步。

---

## 实战：LangGraph 编排示例

LangGraph 是 LangChain 团队推出的工作流编排框架，支持循环和状态持久化：

```typescript
import { StateGraph } from '@langchain/langgraph';

// 定义状态
const graph = new StateGraph({
  channels: {
    input: { value: null },
    classification: { value: null },
    answer: { value: null },
  },
});

// 添加节点（Agent）
graph.addNode('classify', classifyAgent);
graph.addNode('tech_support', techAgent);
graph.addNode('order_support', orderAgent);
graph.addNode('generate_reply', replyAgent);

// 定义边（流转规则）
graph.addEdge('__start__', 'classify');
graph.addConditionalEdges('classify', (state) => state.classification, {
  tech: 'tech_support',
  order: 'order_support',
});
graph.addEdge('tech_support', 'generate_reply');
graph.addEdge('order_support', 'generate_reply');
graph.addEdge('generate_reply', '__end__');

// 编译执行
const app = graph.compile();
const result = await app.invoke({ input: '我的订单怎么还没发货？' });
```

---

## 错误处理与容错

生产环境必须考虑 Agent 失败的情况：

1. **重试机制**：网络超时自动重试 3 次
2. **降级策略**：主 Agent 失败时切换到备用方案
3. **人工兜底**：关键节点要求人工确认
4. **超时控制**：单步执行不超过 30 秒

```typescript
async function runWithRetry(agent, input, maxRetries = 3) {
  for (let i = 0; i < maxRetries; i++) {
    try {
      return await Promise.race([
        agent.run(input),
        new Promise((_, reject) =>
          setTimeout(() => reject(new Error('Timeout')), 30000)
        ),
      ]);
    } catch (e) {
      if (i === maxRetries - 1) throw e;
      await sleep(1000 * (i + 1)); // 指数退避
    }
  }
}
```

---

## 写在最后

工作流编排的本质是**软件工程中的"分而治之"思想在 AI 领域的延伸**。好的编排不是让 Agent 越多越好，而是让每个 Agent 职责单一、边界清晰。

下一篇，我们将探讨 **AI 应用的成本控制与性能优化**——如何把 Token 消耗降下来，把响应速度提上去。
